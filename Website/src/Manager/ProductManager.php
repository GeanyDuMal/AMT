<?php

namespace App\Manager;

use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectRepository;
use JetBrains\PhpStorm\Pure;

class ProductManager
{
    public EntityManagerInterface $manager;
    public ObjectRepository $productRepository;

    public function __construct(EntityManagerInterface $managerController)
    {
        $this->manager = $managerController;
        $this->productRepository = $this->manager->getRepository(Product::class);
    }

    public function persist(Product $product){
         if ($this->verifProduct($product)){
             $this->replaceImageIfEmpty($product);

             $this->manager->persist($product);
             $this->manager->flush();
         }
    }

    /**
     * @param Product $product
     * @param String $productType
     * @param String $productName
     * @param int $productStock
     * @param String $imageLink
     * @return void
     */
    public function setData(Product $product, String $productType, String $productName, int $productStock, String $imageLink):void {
        $product->setName($productName)
            ->setImageLink($imageLink)
            ->setQuantityStock($productStock)
            ->setProductType($productType);
    }

    #[Pure]
    public function verifProduct(Product $product): bool
    {
        return($product->getQuantityStock() >= 0 && $product->getProductType() != null && trim($product->getName()) != "");
    }

    public function restockProduct(Product $product, $quantityToRestock){
        if ($quantityToRestock > 0){
            $product->setQuantityStock($product->getQuantityStock() + $quantityToRestock);
            $this->persist($product);
        }
    }

    public function replaceImageIfEmpty(Product $product)
    {
        if($product->getImageLink() == null || $product->getImageLink() == ""){
            $product->setImageLink('https://a2mo-197c6.kxcdn.com/wp-content/uploads/2021/10/placeholder1.png');
        }
    }
}