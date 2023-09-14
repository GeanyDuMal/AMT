<?php

namespace App\Manager;

use App\Entity\Client;
use App\Entity\Ordered;
use App\Entity\Price;
use App\Entity\Purchase;
use App\Utils\Enum\ClientType;
use App\Utils\Enum\PaymentType;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;

class OrderedManager {
    private EntityManagerInterface $manager;

    public function __construct(EntityManagerInterface $entityManager) {
        $this->manager = $entityManager;
    }

    public function persist(Ordered $ordered): void {
        if ($this->verifyOrder($ordered)) {
            $this->manager->persist($ordered);
            $this->manager->flush();
        }
    }

    public function remove(Ordered $ordered): void {
        $this->manager->remove($ordered);
        $this->manager->flush();
    }

    /**
     * Remove the ordered, refound the product and the Client in function of this payment
     * @param Ordered $order
     * @return void
     */
    public function removeWithRestore(Ordered $order): void {
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

            $client->setBalance(floatval($client->getBalance()) + $montant);
            if ($montant > 1) {
                $client->setFidelityPoint($client->getFidelityPoint() - $montant * 10);
            }

            $clientManager->persist($client);
        }

        //Supprimer de la base de données
        foreach ($purchaseList as $purchase) {
            $purchaseManager->removeWithRestore($purchase);
        }
        $this->remove($order);
    }

    /**
     * @param Ordered $ordered
     * @param Client|null $client
     * @param string $paymentType
     * @param DateTime|null $date
     * @return void
     */
    public function setData(Ordered $ordered, ?Client $client, string $paymentType, ?DateTime $date): void {
        if (!$date) {
            $date = new DateTime("now");
        }

        $ordered->setClient($client)
                ->setPaymentType($paymentType)
                ->setOrderedAt($date);
    }

    /**
     * Reduce the balance of the Client if he paid with his balance
     * @param Ordered $order
     * @return void
     */
    public function reduceBalanceIfNecessary(Ordered $order): void {
        $clientManager = new ClientManager($this->manager);

        if ($order->getPaymentType() == PaymentType::SOLDE && $order->getClient() != null) {
            $montantTotal = $this->montantTotal($order);

            $order->getClient()->setBalance($order->getClient()->getBalance() - $montantTotal);
            $clientManager->persist($order->getClient());
        }
    }

    /**
     * @param Ordered $ordered
     * @return float The total amount of an ordered
     */
    public function montantTotal(Ordered $ordered): float {
        //$purchaseRepository = $this->manager->getRepository(Purchase::class);
        $priceManager = new PriceManager($this->manager);
        $montantTotal = 0;
        //$allOrderPurchase = $purchaseRepository->findBy(["ordered" => $ordered]);
        $allOrderPurchase = $ordered->getPurchases();

        foreach ($allOrderPurchase as $purchase) {
            $clientType = $priceManager->getClientTypeUsedForPrice($ordered->getClient());

            $montantTotal = $montantTotal + $priceManager->getPriceByProductAndClientType($purchase->getProduct(), $clientType) * $purchase->getQuantity();
        }
        return $montantTotal;
    }

    /**
     * Increase the fidelity of the Client specified in the ordered
     * @param Ordered $ordered
     * @return void
     */
    public function addFidelityToClient(Ordered $ordered): void {
        if ($ordered->getClient() != null) {
            $montant = $this->montantTotal($ordered);
            $clientManager = new ClientManager($this->manager);

            $clientManager->addFidelityPoint($montant, $ordered->getClient());
        }
    }

    /**
     * @param int $amountOrdered
     * @param Client|null $client Client
     * @return array An array of payment type that are allowed fot this Ordered
     */
    public function getAllowedPaymentType(float $amountOrdered, Client $client = null): array {
        $paymentTypeList = PaymentType::getAll();

        foreach ($paymentTypeList as $paymentType) {
            switch ($paymentType) {
                case "Solde" :
                {
                    if ($client == null || $client->getBalance() < $amountOrdered) {
                        unset($paymentTypeList[array_search($paymentType, $paymentTypeList, true)]);
                    }
                    break;
                }
                case "Carte Bancaire" :
                {
                    if ($amountOrdered < 1) {
                        unset($paymentTypeList[array_search($paymentType, $paymentTypeList, true)]);
                    }
                    break;
                }
            }
        }
        return $paymentTypeList;
    }

    /**
     * Verify if the Date != null, PaymentType != null
     * @param Ordered $ordered
     * @return bool
     */
    public function verifyOrder(Ordered $ordered): bool {
        return ($ordered->getOrderedAt() != null && $ordered->getPaymentType() != null);
    }

    /**
     * Remove the purchases from the current $ordered
     * This method don't persist the $ordered
     * @param Ordered $ordered
     * @return void
     */
    public function clearPurchases(Ordered $ordered): void {
        foreach ($ordered->getPurchases() as $purchase) {
            $ordered->removePurchase($purchase);
        }
    }
}