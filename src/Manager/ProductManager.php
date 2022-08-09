<?php

namespace App\Manager;

use App\Entity\Product;
use App\Entity\Purchase;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectRepository;

class ProductManager
{
    public EntityManagerInterface $manager;
    public ObjectRepository $productRepository;

    public function __construct(EntityManagerInterface $managerController)
    {
        $this->manager = $managerController;
        $this->productRepository = $this->manager->getRepository(Product::class);
    }

    public function persist(Product $product): void
    {
        if ($this->verifyProduct($product)) {
            $this->replaceImageIfEmpty($product);

            $this->manager->persist($product);
            $this->manager->flush();
        }
    }

    /**
     * A utiliser uniquement lors d'une update
     * @param Product $product
     * @return void
     */
    public function persistCascade(Product $product)
    {
        $this->persist($product);
        $priceManager = new PriceManager($this->manager);

        foreach ($product->getPrices() as $price){
            $priceManager->persist($price);
        }
    }

    /**
     * Remove the Product and all the Purchase linked
     * @param Product $product
     * @return void
     */
    public function remove(Product $product): void
    {
        $purchaseRepository = $this->manager->getRepository(Purchase::class);
        $purchaseManager = new PurchaseManager($this->manager);
        $priceManager = new PriceManager($this->manager);
        $purchaseLinked = $purchaseRepository->findBy(["product" => $product]);

        //On supprime les achats liés au produit supprimé
        foreach ($purchaseLinked as $purchase){
            $purchaseManager->remove($purchase);
        }

        foreach ($product->getPrices() as $price){
            $priceManager->remove($price);
        }

        $this->manager->remove($product);
        $this->manager->flush();
    }

    /**
     * @param Product $product
     * @param String $productType
     * @param String $productName
     * @param int $productStock
     * @param String $imageLink
     * @return void
     */
    public function setData(Product $product, string $productType, string $productName, int $productStock, string $imageLink): void
    {
        $product->setName($productName)
            ->setImageLink($imageLink)
            ->setQuantityStock($productStock)
            ->setProductType($productType);
    }

    /**
     * @param Product $product
     * @return bool
     */
    public function verifyProduct(Product $product): bool
    {
        return ($product->getQuantityStock() >= 0 && $product->getProductType() != null && trim($product->getName()) != "");
    }

    /**
     * @param Product $product
     * @param int $quantityToRestock
     * @return void
     */
    public function restockProduct(Product $product, int $quantityToRestock): void
    {
        if ($quantityToRestock > 0) {
            $product->setQuantityStock($product->getQuantityStock() + $quantityToRestock);
            $this->persist($product);
        }
    }

    /**
     * @param Product $product
     * @return void
     */
    public function replaceImageIfEmpty(Product $product)
    {
        if ($product->getImageLink() == "") {
            $product->setImageLink('https://a2mo-197c6.kxcdn.com/wp-content/uploads/2021/10/placeholder1.png');
        }
    }



    /**
     * @param string $link
     * @return string The link where the picture is stored
     */
    public function downloadPicture(string $link): string
    {
        $lastProduct = $this->productRepository->findOneBy([], ["id" => "DESC"]);
        $newId = 1;

        if ($lastProduct){
            $newId = $lastProduct->getId()+1;
        }

        $location = "/img/entity/product/img_".$newId.".png";

        $pictureUtils = new PictureUtils();

        return $pictureUtils->downloadPicture($link, $location);
    }
}