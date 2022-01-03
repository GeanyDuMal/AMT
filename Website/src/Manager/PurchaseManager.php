<?php

namespace App\Manager;

use App\Entity\Client;
use App\Entity\ClientType;
use App\Entity\PaymentType;
use App\Entity\Price;
use App\Entity\Product;
use App\Entity\Purchase;
use Doctrine\ORM\EntityManagerInterface;

class PurchaseManager
{
    public EntityManagerInterface $manager;

    public function __construct(EntityManagerInterface $managerController)
    {
        $this->manager = $managerController;
    }

    public function persist(Purchase $purchase): void{
        if ($this->verifyDisponibilityProduct($purchase)){
            $this->manager->persist($purchase);
            $this->manager->flush();
        }
    }

    public function removeWithRestore(Purchase $purchase): void{
        $product = $purchase->getProduct();
        $productManager = new ProductManager($this->manager);

        //Restock le produit de la quantity
        $productManager->restockProduct($product, $purchase->getQuantity());

        $this->manager->remove($purchase);
        $this->manager->flush();
    }

    public function verifyDisponibilityProduct(Purchase $purchase): bool{
        $product = $purchase->getProduct();

        return ($product->getQuantityStock() >= $purchase->getQuantity());
    }
}