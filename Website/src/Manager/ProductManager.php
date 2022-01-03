<?php

namespace App\Manager;

use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;

class ProductManager
{
    public EntityManagerInterface $manager;

    public function __construct(EntityManagerInterface $managerController)
    {
        $this->manager = $managerController;
    }

    public function persist(Product $product){
         if ($this->verifProduct($product)){
             $this->manager->persist($product);
             $this->manager->flush();
         }
    }

    public function verifProduct(Product $product): bool
    {
        return($product->getQuantityStock()>=0 && $product->getProductType() != null && trim($product->getName()) != "");
    }

    public function restockProduct(Product $product, $quantityToRestock){
        if ($quantityToRestock > 0){
            $product->setQuantityStock($product->getQuantityStock() + $quantityToRestock);
            $this->persist($product);
        }
    }
}