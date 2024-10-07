<?php

namespace App\Manager;

use App\Entity\Client;
use App\Entity\Ordered;
use App\Entity\Purchase;
use App\Repository\OrderedRepository;
use App\Utils\Enum\ClientType;
use App\Utils\Enum\OrderedStatus;
use App\Utils\Enum\PaymentType;
use DateTime;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityManagerInterface;

class OrderedManager {
    private EntityManagerInterface $manager;
    private OrderedRepository $orderedRepository;
    private PurchaseManager $purchaseManager;
    private ClientManager $clientManager;

    public function __construct(EntityManagerInterface $manager) {
        $this->manager = $manager;
        $this->purchaseManager = new PurchaseManager($manager);
        $this->clientManager = new ClientManager($manager);
        $this->orderedRepository = $this->manager->getRepository(Ordered::class);
    }

    public function persist(Ordered $ordered): void {
        $this->manager->persist($ordered);
        $this->manager->flush();
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
        $montant = $this->getMontantTotal($order);

        $purchaseList = $purchaseRepository->findBy(["ordered" => $order]);

        /*
         * Permet de restore le client s'il est mentionné dans la commande
         * Et qu'il a payé avec son solde
         */


        //Supprimer de la base de données
        foreach ($purchaseList as $purchase) {
            $purchaseManager->removeWithRestore($purchase);
        }
    }

    /**
     * @param Ordered $ordered
     * @param Client|null $client
     * @param string $paymentType
     * @param DateTime|null $date
     * @param string $status
     * @param string $clientType
     * @return void
     */
    public function setData(Ordered $ordered, ?Client $client, string $paymentType, ?DateTime $date, string $status, string $clientType): void {
        if (!$date) {
            $date = new DateTime("now");
        }

        $ordered->setClient($client)
                ->setPaymentType($paymentType)
                ->setOrderedAt($date)
                ->setStatus($status)
                ->setClientTypeAtOrder($clientType);
    }

    /**
     * Reduce the balance of the Client if he paid with his balance
     * @param Ordered $order
     * @return void
     */
    public function reduceBalanceIfNecessary(Ordered $order): void {
        $clientManager = new ClientManager($this->manager);

        if ($order->getPaymentType() == PaymentType::SOLDE && $order->getClient() != null) {
            $montantTotal = $this->getMontantTotal($order);

            $order->getClient()->setBalance($order->getClient()->getBalance() - $montantTotal);
            $clientManager->persist($order->getClient());
        }
    }

    /**
     * @param Ordered $ordered
     * @return float The total amount of an ordered
     */
    public function getMontantTotal(Ordered $ordered): float {
        $montantTotal = 0;

        foreach ($ordered->getPurchases() as $purchase) {
            $montantTotal = $montantTotal + ($purchase->getUnitaryPrice() * $purchase->getQuantity());
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
            $montant = $this->getMontantTotal($ordered);
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

    public function createOrdered(?Client $client, Collection $purchases): Ordered {
        $ordered = new Ordered();

        $ordered->setClient($client)
                ->setOrderedAt(new DateTime())
                ->setClientTypeAtOrder($this->getClientTypeUsedForOrdered($client))
                ->setPurchases($purchases)
                ->setStatus(OrderedStatus::WAITING_PAYMENT);


        return $ordered;
    }

    /**
     * Retourne le type de prix concerné par le type de client passé en paramètre
     * @param Client|null $client
     * @return string
     */
    public function getClientTypeUsedForOrdered(?Client $client): string {
        if ($client) {
            switch ($client->getClientType()) {
                case ClientType::ASSOCIATION :
                    $clientTypeReturn = ClientType::ASSOCIATION;
                    break;

                case ClientType::COTISANT :
                    $parameterManager = new ParameterManager(($this->manager));
                    $parameter = $parameterManager->getParameter();

                    if ($parameter->isCotisantActivated()) {
                        $clientTypeReturn = ClientType::ASSOCIATION;
                    } else {
                        $clientTypeReturn = ClientType::ETUDIANT;
                    }
                    break;

                default:
                    $clientTypeReturn = ClientType::ETUDIANT;
            }
        } else {
            $clientTypeReturn = ClientType::ETUDIANT;
        }

        return $clientTypeReturn;
    }

    public function getOrderedById(int $id): ?Ordered {
        return $this->orderedRepository->find($id);
    }

    public function cancel(Ordered $ordered): void {
        $ordered->setStatus(OrderedStatus::CANCELED);

        $this->persist($ordered);
    }

    public function refund(Ordered $ordered): void {
        if ($ordered->getClient() != null &&
            $ordered->getPaymentType() == PaymentType::SOLDE) {
            $client = $ordered->getClient();
            $montantTotal = $this->getMontantTotal($ordered);

            $client->setBalance(floatval($client->getBalance()) + $montantTotal);
            if ($montantTotal > 1) {
                $client->setFidelityPoint($client->getFidelityPoint() - $montantTotal * 10);
            }

            foreach ($ordered->getPurchases() as $purchase) {
                $this->purchaseManager->refund($purchase);
            }

            $ordered->setStatus(OrderedStatus::REFUNDED);

            $this->clientManager->persist($client);
            $this->persist($ordered);
        }
    }
}