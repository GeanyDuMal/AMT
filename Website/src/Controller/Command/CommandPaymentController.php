<?php

namespace App\Controller\Command;

use App\Entity\Client;
use App\Entity\ClientType;
use App\Entity\Command;
use App\Entity\PaymentType;
use App\Entity\Price;
use App\Entity\Product;
use App\Entity\Purchase;
use App\Manager\OrderManager;
use App\Manager\PurchaseManager;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CommandPaymentController extends AbstractController
{
    /**
     * @Route("/command/payment/{productOrderedSerialized}&{idClient}", name="commandPayment")
     */
    public function index($productOrderedSerialized, $idClient, EntityManagerInterface $manager, Request $request): Response
    {
        if (!$this->isGranted('ROLE_ASSOC')){
            return $this->redirectToRoute('home');
        }

        $clientRepository = $manager->getRepository(Client::class);
        $clientTypeRepository = $manager->getRepository(ClientType::class);
        $priceRepository = $manager->getRepository(Price::class);
        $paymentTypeRepository = $manager->getRepository(PaymentType::class);
        $productRepository = $manager->getRepository(Product::class);
        $clientOrder = null;
        $clientType = $clientTypeRepository->findOneBy(["name" => "Etudiant"]);
        $inputParameterBag = $request->request;
        $listProduct = [];
        $productOrderedIdTab = unserialize($productOrderedSerialized);

        //Si l'on a select un client, alors on conserve celui ci + son type
        if ($idClient != "null"){
            $clientOrder = $clientRepository->find($idClient);
            $clientType = $clientOrder->getClientType();
        }

        //Definir le montant pour chaque produit + montant total
        $montantProduct = [];
        $montantTotal = 0;
        foreach ($productOrderedIdTab as $idProduct => $quantity){
            $product = $productRepository->find($idProduct);
            $listProduct[] = $product;

            $montantProduct = $montantProduct + [$idProduct => $priceRepository->findOneBy(['product' => $product,
                'clientType' => $clientType])->getPrice() * $quantity];

            $montantTotal = $montantTotal + $montantProduct[$idProduct];
        }

        //Recuperer les moyens de paiement possible
        $paymentTypeDispo = $paymentTypeRepository->findAll();
        if ($clientOrder == null || $clientOrder->getBalance() < $montantTotal){
            foreach ($paymentTypeDispo as $paymentType){
                if ($paymentType->getName() == "Solde"){
                    unset($paymentTypeDispo[array_search($paymentType, $paymentTypeDispo, true)]);
                }
            }
        }

        //Si l'on a cliqué sur un bouton sur la page Payment
        if ($inputParameterBag->get('payement_type')){
            $purchaseManager = new PurchaseManager($manager);
            $orderManager = new OrderManager($manager);
            $paymentTypeChose = $paymentTypeRepository->find($inputParameterBag->get('payement_type'));

            //Verif si Solde

            $order = new Command();
            $order->setClient($clientOrder)
                    ->setOrderedAt(new DateTime("now"))
                    ->setPaymentType($paymentTypeChose);
            $orderManager->persist($order);

            foreach ($productOrderedIdTab as $productId => $quantity) {
                $product = $productRepository->find($productId);

                $purchase = new Purchase();
                $purchase->setProduct($product)
                    ->setCommand($order)
                    ->setQuantity($quantity);

                if ($purchaseManager->verifyDisponibilityProduct($purchase)){
                    $purchaseManager->persist($purchase);
                }
            }

            $orderManager->reduceBalanceIfNecessary($order);
            $orderManager->addFidelityToClient($order);
        //rediriger ailleurs
            return $this->redirectToRoute("orderCreate", ["succes" => 1]);
        }

        return $this->render('command/payment.html.twig', [
            'listProduct' => $listProduct,
            'productQuantity' => $productOrderedIdTab,
            'montantProduct' => $montantProduct,
            'montantTotal' => $montantTotal,
            'client' => $clientOrder,
            "paymentTypeList" => $paymentTypeDispo
        ]);
    }
}
