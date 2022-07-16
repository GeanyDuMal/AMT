<?php

namespace App\Manager;

use App\Entity\Client;
use App\Entity\Ordered;
use App\Entity\Price;
use App\Entity\Product;
use App\Entity\Purchase;
use App\Utils\Enum\ClientType;
use App\Utils\Enum\PaymentType;
use Doctrine\ORM\EntityManagerInterface;
use JetBrains\PhpStorm\Pure;

class OrderedManager
{
    public EntityManagerInterface $manager;

    public function __construct(EntityManagerInterface $managerController)
    {
        $this->manager = $managerController;
    }

    public function persist(Ordered $order): void
    {
        if ($this->verifyOrder($order)) {
            $this->manager->persist($order);
            $this->manager->flush();
        }
    }

    public function removeWithRestore(Ordered $order): void
    {
        $purchaseManager = new PurchaseManager($this->manager);
        $clientManager = new ClientManager($this->manager);
        $purchaseRepository = $this->manager->getRepository(Purchase::class);
        $montant = $this->montantTotal($order);

        $purchaseList = $purchaseRepository->findBy(["ordered" => $order]);

        /*
         * Permet de restore le client s'il est mentionné dans la commande
         * Et qu'il a payé avec son solde
         */

        if ($order->getClient() != null &&
            $order->getPaymentType() == PaymentType::SOLDE) {
            $client = $order->getClient();

            $client->setBalance($client->getBalance() + $montant);
            if ($montant > 1) {
                $client->setFidelityPoint($client->getFidelityPoint() - $montant * 10);
            }

            $clientManager->persist($client);
        }

        //Supprimer de la base de données
        foreach ($purchaseList as $purchase) {
            $purchaseManager->removeWithRestore($purchase);
        }
        $this->manager->remove($order);
        $this->manager->flush();
    }


    public function reduceBalanceIfNecessary(Ordered $order): void
    {
        $clientManager = new ClientManager($this->manager);

        if ($order->getPaymentType() == PaymentType::SOLDE && $order->getClient() != null) {
            $montantTotal = $this->montantTotal($order);

            $order->getClient()->setBalance($order->getClient()->getBalance() - $montantTotal);
            $clientManager->persist($order->getClient());
        }
    }

    public function montantTotal(Ordered $ordered): float
    {
        $purchaseRepository = $this->manager->getRepository(Purchase::class);
        $priceRepository = $this->manager->getRepository(Price::class);
        $priceManager = new PriceManager($this->manager);
        $montantTotal = 0;
        $allOrderPurchase = $purchaseRepository->findBy(["ordered" => $ordered]);

        foreach ($allOrderPurchase as $purchase) {
            $clientType = ClientType::ETUDIANT;

            if ($ordered->getClient() != null) {
                $clientType = $priceManager->getClientTypeUseForPrice($ordered->getClient()->getClientType());
            }

            $montantTotal = $montantTotal + $priceRepository->findOneBy(["product" => $purchase->getProduct(),
                    "clientType" => $clientType])->getPrice() * $purchase->getQuantity();
        }
        return $montantTotal;
    }

    public function addFidelityToClient(Ordered $ordered): void
    {
        if ($ordered->getClient() != null) {
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
        $priceManager = new PriceManager($this->manager);

        if ($client != null) {
            $clientType = $priceManager->getClientTypeUseForPrice($client->getClientType());
        } else {
            $clientType = ClientType::ETUDIANT;
        }

        $montant = 0;
        $productRepository = $this->manager->getRepository(Product::class);
        $priceRepository = $this->manager->getRepository(Price::class);

        foreach ($purchaseList as $productId => $quantity) {
            $product = $productRepository->find($productId);
            $montant = $montant + $priceRepository->findOneBy(["product" => $product, "clientType" => $clientType])->getPrice() * $quantity;
        }

        $paymentTypeList = PaymentType::getAll();

        foreach ($paymentTypeList as $paymentType) {
            switch ($paymentType) {
                case "Solde" :
                {
                    if ($client == null || $client->getBalance() < $montant) {
                        unset($paymentTypeList[array_search($paymentType, $paymentTypeList, true)]);
                    }
                    break;
                }
                case "Carte Bancaire" :
                {
                    if ($montant < 1) {
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
     * Verify if the Date != null, PaymentType != null
     */
    #[Pure]
    public function verifyOrder(Ordered $ordered): bool
    {
        return ($ordered->getOrderedAt() != null && $ordered->getPaymentType() != null);
    }
}