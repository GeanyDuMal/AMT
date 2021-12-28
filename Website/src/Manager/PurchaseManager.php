<?php

namespace App\Manager;

use App\Entity\Purchase;
use Doctrine\ORM\EntityManagerInterface;

class PurchaseManager
{
    public $manager;

    public function __construct(EntityManagerInterface $managerController)
    {
        $this->manager = $managerController;
    }

    public function verifyDisponibilityProduct(Purchase $purchase): bool{
        $product = $purchase->getProduct();

        return ($product->getQuantityStock() >= $purchase->getQuantity());
    }

    public function persist(Purchase $purchase): void{
        if ($this->verifyDisponibilityProduct($purchase)){
            $this->manager->persist($purchase);
            $this->manager->flush();
        }
    }
}