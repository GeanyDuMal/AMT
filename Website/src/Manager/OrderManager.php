<?php

namespace App\Manager;

use App\Entity\ClientType;
use App\Entity\Command;
use App\Entity\PaymentType;
use App\Entity\Price;
use App\Entity\Purchase;
use Doctrine\ORM\EntityManagerInterface;

class OrderManager
{
    public $manager;

    public function __construct(EntityManagerInterface $managerController)
    {
        $this->manager = $managerController;
    }

    public function reduceBalanceIfNecessary(Command $order): void{
        $paymentTypeRepository = $this->manager->getRepository(PaymentType::class);
        $clientManager = new ClientManager($this->manager);
        if ($order->getPaymentType() == $paymentTypeRepository->findOneBy(["name" => "Solde"])
            && $order->getClient() != null){
            $montantTotal = $this->montantTotal($order);

            $order->getClient()->setBalance($order->getClient()->getBalance() - $montantTotal);
            $clientManager->persist($order->getClient());
        }
    }

    public function montantTotal(Command $order): float{
        $purchaseRepository = $this->manager->getRepository(Purchase::class);
        $priceRepository = $this->manager->getRepository(Price::class);
        $montantTotal = 0;

        $allOrderPurchase = $purchaseRepository->findBy(["command" => $order]);

        foreach ($allOrderPurchase as $purchase){
            $clientTypeRepository = $this->manager->getRepository(ClientType::class);
            $clientType = $clientTypeRepository->findOneBy(["name" => "Etudiant"]);
            if ($order->getClient() != null){
                $clientType = $order->getClient()->getClientType();
            }

            $montantTotal = $montantTotal + $priceRepository->findOneBy(["product" => $purchase->getProduct(),
                    "clientType" => $clientType])->getPrice();
        }

        return $montantTotal;
    }

    public function addFidelityToClient(Command $order):void{
        if ($order->getClient() != null){
            $montant = $this->montantTotal($order);
            $clientManager = new ClientManager($this->manager);

            if ($montant >= 1){
                $clientManager->addFidelityPoint($montant, $order->getClient());
            }
        }
    }

    public function verifyOrder(Command $order): bool{
        return ($order->getOrderedAt() != null && $order->getPaymentType() != null);
    }

    public function persist(Command $order): void{
        if ($this->verifyOrder($order)){
            $this->manager->persist($order);
            $this->manager->flush();
        }
    }
}