<?php

namespace App\Manager;

use App\Entity\Product;
use App\Entity\ProductType;
use App\Repository\ProductTypeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectRepository;
use Symfony\Component\HttpFoundation\Request;

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
             $this->manager->persist($product);
             $this->manager->flush();
         }
    }

    /**
     * @param ProductTypeRepository $productTypeRepository
     * @param Product $product
     * @param String $productType
     * @param String $productName
     * @param int $productStock
     * @param String $imageLink
     * @return void
     */
    public function setData(ProductTypeRepository $productTypeRepository, Product $product, String $productType, String $productName, int $productStock, String $imageLink){
                            $type = $productTypeRepository->findOneBy(["name"=>$productType]);

        $product->setName($productName)
            ->setImageLink($imageLink)
            ->setQuantityStock($productStock)
            ->setProductType($type);
    }

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
}