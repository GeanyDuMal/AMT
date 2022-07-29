<?php

namespace App\Controller\Ordered;

use App\Entity\Ordered;
use App\Entity\Purchase;
use App\Manager\OrderedManager;
use App\Manager\PriceManager;
use App\Manager\PurchaseManager;
use App\Repository\ClientRepository;
use App\Repository\PriceRepository;
use App\Repository\ProductRepository;
use App\Utils\Enum\ClientType;
use App\Utils\Enum\SymfonyRole;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PaymentOrderedController extends AbstractController
{
    /**
     * @Route("/ordered/payment/{!productOrderedSerialized}&{!idClient}", name="orderedPayment")
     */
    public function index($productOrderedSerialized, $idClient, EntityManagerInterface $manager, Request $request,
        ClientRepository $clientRepository, PriceRepository $priceRepository, ProductRepository $productRepository): Response
    {
        if (!$this->isGranted(SymfonyRole::ASSOC)) {
            return $this->redirectToRoute('home');
        }

        $purchaseManager = new PurchaseManager($manager);
        $orderManager = new OrderedManager($manager);
        $priceManager = new PriceManager($manager);
        $clientOrder = null;
        $clientType = ClientType::ETUDIANT;
        $inputParameterBag = $request->request;
        $listProduct = [];
        $productOrderedIdTab = unserialize($productOrderedSerialized);

        //Si l'on a select un client, alors on conserve celui ci + son type
        if ($idClient != "null") {
            $clientOrder = $clientRepository->find($idClient);
            $clientType = $priceManager->getClientTypeUseForPrice($clientOrder->getClientType());
        }

        /*
         * Definir le montant pour chaque produit + montant total
         * tout ca dans un tableau
         */
        $montantProduct = [];
        $montantTotal = 0;
        foreach ($productOrderedIdTab as $idProduct => $quantity) {
            $product = $productRepository->find($idProduct);
            $listProduct[] = $product;

            $montantProduct = $montantProduct + [$idProduct => $priceRepository->findOneBy(['product' => $product,
                        'clientType' => $clientType])->getPrice() * $quantity];

            $montantTotal = $montantTotal + $montantProduct[$idProduct];
        }

        //Recuperer les moyens de paiement possible
        $paymentTypeList = $orderManager->getAllowedPaymentType($productOrderedIdTab, $clientOrder);

        //Si l'on a cliqué sur un bouton sur la page Payment
        if ($inputParameterBag->get('payement_type')) {
            $paymentTypeChose = $inputParameterBag->get('payement_type');

            /*
             * Permet de verifier si le produit commandé est en stock
             * Prevent si l'utilisateur clique plusieurs fois sur le bouton valider
             */
            foreach ($productOrderedIdTab as $productId => $quantity) {
                $product = $productRepository->find($productId);
                if ($product->getQuantityStock() == 0) {
                    return $this->redirectToRoute("menuOrdered", [
                        "message" => "Le produit commandé n'est plus disponible !"
                    ]);
                }
            }

            //Creer la commande
            $order = new Ordered();
            $order->setClient($clientOrder)
                ->setOrderedAt(new DateTime("now"))
                ->setPaymentType($paymentTypeChose);
            $orderManager->persist($order);

            //Creer tout les achats
            foreach ($productOrderedIdTab as $productId => $quantity) {
                $product = $productRepository->find($productId);

                $purchase = new Purchase();
                $purchase->setProduct($product)
                    ->setOrdered($order)
                    ->setQuantity($quantity);

                if ($purchaseManager->verifyDisponibilityProduct($purchase)) {
                    $purchaseManager->persist($purchase);
                }
            }

            $orderManager->reduceBalanceIfNecessary($order);
            $orderManager->addFidelityToClient($order);

            //rediriger vers l'interface de creation de commande
            return $this->redirectToRoute("createOrdered", [
                "message" => "Commande réussie !"
            ]);
        }

        return $this->render('ordered/PaymentOrdered.html.twig', [
            'listProduct' => $listProduct,
            'productQuantity' => $productOrderedIdTab,
            'montantProduct' => $montantProduct,
            'montantTotal' => $montantTotal,
            'client' => $clientOrder,
            "paymentTypeList" => $paymentTypeList
        ]);
    }
}
