<?php

namespace App\Manager;

use App\Entity\Product;
use App\Entity\Purchase;
use App\Repository\ProductRepository;
use App\Utils\PictureUtils;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectRepository;

class ProductManager
{
    public EntityManagerInterface $manager;
    public ProductRepository $productRepository;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->manager = $entityManager;
        $this->productRepository = $this->manager->getRepository(Product::class);
    }

    public function persist(Product $product): void
    {
        if ($this->verifyProduct($product)) {

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
        $pictureUtils = new PictureUtils();

        $pictureUtils->deletePicture($product->getImageLink());

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
        return ($product->getQuantityStock() >= 0 && $product->getProductType() != null && trim($product->getName()) != ""
            && $product->getImageLink() != null);
    }

    /**
     * @param Product $product
     * @return bool
     */
    public function verifyEditedProductAlreadyExist(Product $product): bool
    {
        $idProduct = $product->getId();
        $nameProduct = $product->getName();

        $productSameName = $this->productRepository->findOneBy(["name" => $nameProduct]);

        return ($productSameName && !($idProduct == $productSameName->getId()) && ($productSameName->getName() == $nameProduct));
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
     * @param string $link
     * @param Product|null $productActual (default = null)
     * @return string The link where the picture is stored
     */
    public function downloadPicture(string $link, ?Product $productActual = null): string
    {
        /**
         * @newId corresponds a l'ID de $productActual s'il est enregistré sinon le dernier ID enregistré+1
         */
        $newId = 1;

        if (trim($link) == "") {
            $link = "/";
        }

        if ($productActual){
            $lastProduct = $this->productRepository->findOneBy([
                "name" => $productActual->getName(),
                "productType" => $productActual->getProductType()]);
        } else {
            $lastProduct = $this->productRepository->findOneBy([], ["id" => "DESC"]);
        }

        if ($lastProduct){
            $newId = $lastProduct->getId()+1;
        }

        $location = "/img/entity/product/img_".$newId.".png";

        $pictureUtils = new PictureUtils();

        return $pictureUtils->downloadPicture($link, $location);
    }

    /**
     * @param Product $product
     * @param string $newPictureLink
     * @return void
     */
    public function switchPicture(Product $product, string $newPictureLink)
    {
        $actualLink = $product->getImageLink();
        $pictureUtils = new PictureUtils();

        /**
         * TODO: sera a supprimer une fois que toutes les images auront été migrées
         * Permet de gerer les cas des anciennes images
         */
        if (str_starts_with($actualLink, "http") || str_contains($actualLink, "placeholder")) {
            $actualLink = "/img/entity/product/img_".$product->getId().".png";
        }

        $pictureUtils->deletePicture($actualLink);
        $product->setImageLink($pictureUtils->downloadPicture($newPictureLink, $actualLink));
    }
}