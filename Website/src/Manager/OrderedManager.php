<?php

namespace App\Manager;

use App\Entity\Client;
use App\Entity\ClientType;
use App\Entity\Ordered;
use App\Entity\PaymentType;
use App\Entity\Price;
use App\Entity\Product;
use App\Entity\Purchase;
use Doctrine\ORM\EntityManagerInterface;

class OrderedManager
{
    public EntityManagerInterface $manager;

    public function __construct(EntityManagerInterface $managerController)
    {
        $this->manager = $managerController;
    }

    public function persist(Ordered $order): void{
        if ($this->verifyOrder($order)){
            $this->manager->persist($order);
            $this->manager->flush();
        }
    }

    public function removeWithRestore(Ordered $order): void{
        $purchaseManager = new PurchaseManager($this->manager);
        $clientManager = new ClientManager($this->manager);
        $purchaseRepository = $this->manager->getRepository(Purchase::class);
        $paymentTypeRepository = $this->manager->getRepository(PaymentType::class);
        $montant = $this->montantTotal($order);

        $purchaseList = $purchaseRepository->findBy(["ordered" => $order]);

        /*
         * Permet de restore le client s'il est mentionné dans la commande
         * Et qu'il a payé avec son solde
         */

        if ($order->getClient() != null &&
            $order->getPaymentType() == $paymentTypeRepository->findOneBy(["name" => "Solde"]))
        {
            $client = $order->getClient();

            $client->setBalance($client->getBalance() + $montant);
            if ($montant > 1){
                $client->setFidelityPoint($client->getFidelityPoint() - $montant*10);
            }

            $clientManager->persist($client);
        }

        //Supprimer de la base de données
        foreach ($purchaseList as $purchase){
            $purchaseManager->removeWithRestore($purchase);
        }
        $this->manager->remove($order);
        $this->manager->flush();
    }



    public function reduceBalanceIfNecessary(Ordered $order): void{
        $paymentTypeRepository = $this->manager->getRepository(PaymentType::class);
        $clientManager = new ClientManager($this->manager);

        if ($order->getPaymentType() == $paymentTypeRepository->findOneBy(["name" => "Solde"])
            && $order->getClient() != null){
            $montantTotal = $this->montantTotal($order);

            $order->getClient()->setBalance($order->getClient()->getBalance() - $montantTotal);
            $clientManager->persist($order->getClient());
        }
    }

    public function montantTotal(Ordered $ordered): float{
        $purchaseRepository = $this->manager->getRepository(Purchase::class);
        $priceRepository = $this->manager->getRepository(Price::class);
        $montantTotal = 0;
        $allOrderPurchase = $purchaseRepository->findBy(["ordered" => $ordered]);

        foreach ($allOrderPurchase as $purchase){
            $clientTypeRepository = $this->manager->getRepository(ClientType::class);
            $clientType = $clientTypeRepository->findOneBy(["name" => "Etudiant"]);

            if ($ordered->getClient() != null){
                $clientType = $ordered->getClient()->getClientType();
            }

            $montantTotal = $montantTotal + $priceRepository->findOneBy(["product" => $purchase->getProduct(),
                    "clientType" => $clientType])->getPrice()*$purchase->getQuantity();
        }
        return $montantTotal;
    }

    public function addFidelityToClient(Ordered $ordered):void{
        if ($ordered->getClient() != null){
            $montant = $this->montantTotal($ordered);
            $clientManager = new ClientManager($this->manager);

            $clientManager->addFidelityPoint($montant, $ordered->getClient());
        }
    }

    /**
     * @param $purchaseList [productId => quantity]
     * @param Client|null $client Client
     * @return array[PaymentType] $paymentTypeList
     */
    public function getAllowedPaymentType($purchaseList, Client $client = null): array
    {
        $clientTypeRepository = $this->manager->getRepository(ClientType::class);
        if ($client != null) {
            $clientType = $client->getClientType();
        }else{
            $clientType = $clientTypeRepository->findOneBy(["name" => "Etudiant"]);
        }

        $montant = 0;
        $productRepository = $this->manager->getRepository(Product::class);
        $paymentTypeRepository = $this->manager->getRepository(PaymentType::class);
        $priceRepository = $this->manager->getRepository(Price::class);

        foreach ($purchaseList as $productId => $quantity){
            $product = $productRepository->find($productId);
            $montant = $montant + $priceRepository->findOneBy(["product" => $product, "clientType" => $clientType])
                    ->getPrice() * $quantity;
        }

        $paymentTypeList = $paymentTypeRepository->findAll();

        foreach ($paymentTypeList as $paymentType){
            switch ($paymentType->getName()){
                case "Solde" :{
                    if ($client == null || $client->getBalance() < $montant){
                        unset($paymentTypeList[array_search($paymentType, $paymentTypeList, true)]);
                    }
                    break;
                }
                case "Carte Bancaire" :{
                    if ($montant < 1){
                        unset($paymentTypeList[array_search($paymentType, $paymentTypeList, true)]);
                    }
                    break;
                }
            }
        }
        return $paymentTypeList;
    }

    /**
     * @param Ordered $ordered
     * @return bool
     * Verify if the Date != null, PaymentType != (null || "Solde")
     */
    public function verifyOrder(Ordered $ordered): bool{
        $paymentTypeSolde = $this->manager->getRepository(PaymentType::class)->findOneBy(["name" => "Solde"]);

        return ($ordered->getOrderedAt() != null && $ordered->getPaymentType() != null && $ordered->getPaymentType() != $paymentTypeSolde);
    }


}