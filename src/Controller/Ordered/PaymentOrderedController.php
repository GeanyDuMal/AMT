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
use Exception;
use Psr\Container\ContainerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PaymentOrderedController extends AbstractController
{
    /**
     * @Route("/ordered/payment/", name="orderedPayment", methods={"POST"})
     */
    public function index(EntityManagerInterface $manager, Request $request,
        ClientRepository $clientRepository, PriceRepository $priceRepository, ProductRepository $productRepository): Response
    {
        if (!$this->isGranted(SymfonyRole::ASSOC)) {
            return $this->redirectToRoute('home');
        }

        $purchaseManager = new PurchaseManager($manager);
        $orderManager = new OrderedManager($manager);
        $priceManager = new PriceManager($manager);

        $productOrderedAndClient = $request->getSession()->get("productOrderedAndClient");
        $idClient = $productOrderedAndClient["idClient"];
        $clientOrder = null;
        $productOrdered = $this->transformIdProductOrderedTab($productOrderedAndClient["productOrdered"], $productRepository);

        $clientType = ClientType::ETUDIANT;
        $inputParameterBag = $request->request;
        $listProduct = [];

        //Si l'on a select un client, alors on conserve celui ci + son type
        if ($idClient != null) {
            $clientOrder = $clientRepository->find($idClient);

            if ($clientOrder){
                $clientType = $priceManager->getClientTypeUseForPrice($clientOrder->getClientType());
            }
        }

        /*
         * Definir le montant pour chaque produit + montant total
         * tout ca dans un tableau
         */
        $montantProduct = [];
        $montantTotal = 0;
        foreach ($productOrdered as $productOrderedAndQuantity) {
            $product = $productOrderedAndQuantity["product"];

            $montantProduct = $montantProduct + [$product->getId() => $priceRepository->findOneBy(['product' => $product,
                        'clientType' => $clientType])->getPrice() * $productOrderedAndQuantity["quantity"]];

            $montantTotal = $montantTotal + $montantProduct[$product->getId()];
        }

        //Recuperer les moyens de paiement possible
        $paymentTypeList = $orderManager->getAllowedPaymentType($productOrdered, $clientOrder);

        //Si l'on a cliqué sur un bouton sur la page Payment
        if ($inputParameterBag->get('payement_type')) {
            $paymentTypeChose = $inputParameterBag->get('payement_type');

            /*
             * Permet de verifier si le produit commandé est en stock
             * Prevent si l'utilisateur clique plusieurs fois sur le bouton valider
             */
            foreach ($productOrdered as $purchase) {
                $product = $purchase["product"];
                if ($product->getQuantityStock() == 0) {
                    return $this->redirectToRoute("menuOrdered", [
                        "message" => "Le produit commandé n'est plus disponible !"
                    ]);
                }
            }

            //Creer la commande
            $order = new Ordered();
            $orderManager->setData($order, $clientOrder, $paymentTypeChose, new DateTime("now"));
            $orderManager->persist($order);

            //Creer tout les achats
            foreach ($productOrdered as $purchase) {
                $product = $purchase["product"];
                $quantity = $purchase["quantity"];

                $purchase = new Purchase();
                $purchaseManager->setData($purchase, $product, $quantity, $order);

                $purchaseManager->persist($purchase);
            }

            $orderManager->reduceBalanceIfNecessary($order);
            $orderManager->addFidelityToClient($order);

            //rediriger vers l'interface de creation de commande
            return $this->redirectToRoute("createOrdered", [
                "message" => "Commande réussie !"
            ]);
        }

        return $this->render('ordered/PaymentOrdered.html.twig', [
            'productOrdered' => $productOrdered,
            'montantProduct' => $montantProduct,
            'montantTotal' => $montantTotal,
            'client' => $clientOrder,
            "paymentTypeList" => $paymentTypeList
        ]);
    }

    /**
     * @param array $idProductOrderedTab
     * @param ProductRepository $productRepository
     * @return array ["product" => Product, "quantity" => quantity]
     */
    public function transformIdProductOrderedTab(array $idProductOrderedTab, ProductRepository $productRepository): array
    {
        $productOrdered = [];

        foreach ($idProductOrderedTab as $idProductOrdered){
            $product = $productRepository->find($idProductOrdered["idProduct"]);
            $productOrdered[] = ["product" => $product, "quantity" => $idProductOrdered["quantity"]];
        }

        return $productOrdered;
    }
}
