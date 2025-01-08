<?php

namespace App\Manager;

use App\Entity\Ordered;
use App\Entity\Product;
use App\Entity\Purchase;
use App\Utils\Enum\OrderedStatusEnum;
use Doctrine\ORM\EntityManagerInterface;

class PurchaseManager {
    private EntityManagerInterface $manager;

    public function __construct(EntityManagerInterface $entityManager) {
        $this->manager = $entityManager;
    }

    public function persist(Purchase $purchase): void {
        if ($this->verifyDisponibilityProduct($purchase)) {
            if ($purchase->getOrdered()->getStatus() == OrderedStatusEnum::PAID) {
                $this->removeProductQuantity($purchase);
            }

            $this->manager->persist($purchase);
            $this->manager->flush();
        }
    }

    public function remove(Purchase $purchase): void {
        $this->manager->remove($purchase);

        $this->manager->flush();
    }

    /**
     * Remove and restore the product quantity and the client if function of the payment type
     * @param Purchase $purchase
     * @return void
     */
    public function refund(Purchase $purchase): void {
        $product = $purchase->getProduct();
        $productManager = new ProductManager($this->manager);

        //Restock le produit de la quantity
        $productManager->restockProduct($product, $purchase->getQuantity());
    }

    /**
     * @param Purchase $purchase
     * @param Product $product
     * @param int $quantity
     * @param Ordered $ordered
     * @return void
     */
    public function setData(Purchase $purchase, Product $product, int $quantity, Ordered $ordered): void {
        if ($product->getQuantityStock() >= $quantity) {
            $purchase->setQuantity($quantity)
                     ->setProduct($product)
                     ->setOrdered($ordered);
        }
    }

    /**
     * @param Purchase $purchase
     * @return bool
     */
    public function verifyDisponibilityProduct(Purchase $purchase): bool {
        $product = $purchase->getProduct();

        return ($product->getQuantityStock() > 0 && $product->getQuantityStock() >= $purchase->getQuantity());
    }

    public function verifyDisponibilityProducts(array $purchases): bool {
        foreach ($purchases as $purchase) {
            if (!$this->verifyDisponibilityProduct($purchase)) {
                return false;
            }
        }

        return true;
    }


    /**
     * Remove the quantity of the product ordered
     * @param Purchase $purchase
     * @return void
     */
    public function removeProductQuantity(Purchase $purchase): void {
        $product = $purchase->getProduct();
        $product->setQuantityStock($product->getQuantityStock() - $purchase->getQuantity());
    }

    public function createPurchase(Product $product, int $quantity, float $productPrice): Purchase {
        $purchase = new Purchase();

        $purchase->setProduct($product)
                 ->setUnitaryPrice($productPrice)
                 ->setQuantity($quantity);

        return $purchase;
    }
}