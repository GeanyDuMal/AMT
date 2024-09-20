<?php

namespace App\Manager;

use App\Entity\Product;
use App\Entity\Purchase;
use App\Repository\ProductRepository;
use App\Utils\PictureUtils;
use App\Utils\RandomUtils;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class ProductManager {
    private EntityManagerInterface $manager;
    private ProductRepository $productRepository;

    public function __construct(EntityManagerInterface $entityManager) {
        $this->manager = $entityManager;
        $this->productRepository = $this->manager->getRepository(Product::class);
    }

    public function persist(Product $product): void {
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
    public function persistCascade(Product $product): void {
        $this->persist($product);
        $priceManager = new PriceManager($this->manager);

        foreach ($product->getPrices() as $price) {
            $priceManager->persist($price);
        }
    }

    /**
     * Remove the Product and all the Purchase linked
     * @param Product $product
     * @return void
     */
    public function remove(Product $product): void {
        $purchaseRepository = $this->manager->getRepository(Purchase::class);
        $purchaseManager = new PurchaseManager($this->manager);
        $priceManager = new PriceManager($this->manager);
        $purchaseLinked = $purchaseRepository->findBy(["product" => $product]);
        $pictureUtils = new PictureUtils();

        $pictureUtils->deletePicture($product->getImageLink());

        //On supprime les achats liés au produit supprimé
        foreach ($purchaseLinked as $purchase) {
            $purchaseManager->remove($purchase);
        }

        foreach ($product->getPrices() as $price) {
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
    public function setData(Product $product, string $productType, string $productName, int $productStock, ?bool $isActive, string $imageLink = ""): void {
        if(!$isActive) {
            $isActive = false;
        }

        $product->setName($productName)
                ->setImageLink($imageLink)
                ->setQuantityStock($productStock)
                ->setProductType($productType)
                ->setActive($isActive);
    }

    /**
     * @param Product $product
     * @return bool
     */
    public function verifyProduct(Product $product): bool {
        return ($product->getQuantityStock() >= 0 && $product->getProductType() != null && trim($product->getName()) != "" && $product->getImageLink() != null);
    }

    /**
     * @param Product $product
     * @return bool
     */
    public function verifyEditedProductAlreadyExist(Product $product): bool {
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
    public function restockProduct(Product $product, int $quantityToRestock): void {
        if ($quantityToRestock > 0) {
            $product->setQuantityStock($product->getQuantityStock() + $quantityToRestock);
            $this->persist($product);
        }
    }

    public function downloadAndApplyPicture(Product $product, ?UploadedFile $file): string {
        $pictureUtils = new PictureUtils();
        $randomUtils = new RandomUtils();
        $characters = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
        $productExist = (bool)$this->productRepository->findOneBy(["name" => $product->getName(), "productType" => $product->getProductType()]);
        $idUsed = 1;

        if (!$productExist) {
            $lastProduct = $this->productRepository->findOneBy([], ["id" => "DESC"]);

            if ($lastProduct) {
                $idUsed = $lastProduct->getId() + 1;
            }
        } else {
            $idUsed = $product->getId();
        }

        if (($product->getImageLink() != (null || "")) && !str_contains($product->getImageLink(), "placeholder")) {
            $pictureUtils->deletePicture($product->getImageLink());
        }

        $newLocation = "/img/entity/product/img_" . $idUsed . "_" . $randomUtils->randomString(4, $characters) . ".png";
        $product->setImageLink($pictureUtils->downloadPictureFromFile($file, $newLocation));

        return $product->getImageLink();
    }

    public function getAllProductAvailable(): array {
        return $this->productRepository->findAllPositiveStock();
    }
}