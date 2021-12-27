<?php

namespace App\Manager;

use App\Entity\Command;
use Doctrine\ORM\EntityManagerInterface;

class OrderManager
{
    public $manager;

    public function __construct(EntityManagerInterface $managerController)
    {
        $this->manager = $managerController;
    }



    public function verifyOrder(Command $order): bool
    {
        return ($order->getOrderedAt() != null && $order->getPaymentType() != null);
    }

    public function persist(Command $order): void
    {
        if ($this->verifyOrder($order)){
            $this->manager->persist($order);
            $this->manager->flush();
        }
    }
}