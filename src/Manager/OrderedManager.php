<?php

namespace App\Manager;

use App\Entity\Client;
use App\Entity\Ordered;
use App\Entity\Price;
use App\Entity\Purchase;
use App\Repository\OrderedRepository;
use App\Utils\Enum\ClientTypeEnum;
use App\Utils\Enum\OrderedStatusEnum;
use App\Utils\Enum\PaymentTypeEnum;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
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
     * @param Ordered $ordered
     * @param Client|null $client
     * @param PaymentTypeEnum $paymentType
     * @param DateTime|null $date
     * @param OrderedStatusEnum $status
     * @param ClientTypeEnum $clientType
     * @return void
     */
    public function setData(Ordered $ordered, ?Client $client, PaymentTypeEnum $paymentType, ?DateTime $date, OrderedStatusEnum $status, ClientTypeEnum $clientType): void {
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

        if ($order->getPaymentType() == PaymentTypeEnum::SOLDE && $order->getClient() != null) {
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
     * @param float $amountOrdered
     * @param Client|null $client Client
     * @return array An array of payment type that are allowed fot this Ordered
     */
    public function getAllowedPaymentType(float $amountOrdered, Client $client = null): array {
        $paymentTypeList = PaymentTypeEnum::cases();

        foreach ($paymentTypeList as $paymentType) {
            switch ($paymentType) {
                case PaymentTypeEnum::SOLDE : {
                    if ($client == null || $client->getBalance() < $amountOrdered) {
                        unset($paymentTypeList[array_search($paymentType, $paymentTypeList, true)]);
                    }
                    break;
                }
                case PaymentTypeEnum::CARTE_BANCAIRE : {
                    if ($amountOrdered < 1) {
                        unset($paymentTypeList[array_search($paymentType, $paymentTypeList, true)]);
                    }
                    break;
                }
                default: {}
            }
        }
        return $paymentTypeList;
    }

    public function createOrdered(?Client $client, Collection $purchases): Ordered {
        $ordered = new Ordered();

        $ordered->setClient($client)
                ->setOrderedAt(new DateTime())
                ->setClientTypeAtOrder($this->getClientTypeUsedForOrdered($client))
                ->setPurchases($purchases)
                ->setStatus(OrderedStatusEnum::WAITING_PAYMENT);


        return $ordered;
    }

    /**
     * Retourne le type de prix concerné par le type de client passé en paramètre
     * @param Client|null $client
     * @return ClientTypeEnum
     */
    public function getClientTypeUsedForOrdered(?Client $client): ClientTypeEnum {
        if ($client) {
            switch ($client->getClientType()) {
                case ClientTypeEnum::ASSOCIATION :
                    $clientTypeReturn = ClientTypeEnum::ASSOCIATION;
                    break;

                case ClientTypeEnum::COTISANT :
                    $parameterManager = new ParameterManager(($this->manager));
                    $parameter = $parameterManager->getParameter();

                    if ($parameter->isCotisantActivated()) {
                        $clientTypeReturn = ClientTypeEnum::ASSOCIATION;
                    } else {
                        $clientTypeReturn = ClientTypeEnum::ETUDIANT;
                    }
                    break;

                default:
                    $clientTypeReturn = ClientTypeEnum::ETUDIANT;
            }
        } else {
            $clientTypeReturn = ClientTypeEnum::ETUDIANT;
        }

        return $clientTypeReturn;
    }

    public function getOrderedById(int $id): ?Ordered {
        return $this->orderedRepository->find($id);
    }

    public function cancel(Ordered $ordered): void {
        $ordered->setStatus(OrderedStatusEnum::CANCELED);

        $this->persist($ordered);
    }

    public function refund(Ordered $ordered): void {
        if ($ordered->getClient() != null &&
            $ordered->getPaymentType() == PaymentTypeEnum::SOLDE) {
            $client = $ordered->getClient();
            $montantTotal = $this->getMontantTotal($ordered);

            $client->setBalance(floatval($client->getBalance()) + $montantTotal);
            if ($montantTotal > 1) {
                $client->setFidelityPoint($client->getFidelityPoint() - $montantTotal * 10);
            }

            $this->clientManager->persist($client);
        }

        foreach ($ordered->getPurchases() as $purchase) {
            $this->purchaseManager->refund($purchase);
        }

        $ordered->setStatus(OrderedStatusEnum::REFUNDED);
        $this->persist($ordered);
    }
}