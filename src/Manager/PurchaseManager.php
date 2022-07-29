<?php

namespace App\Manager;

use App\Entity\Ordered;
use App\Entity\Purchase;
use Doctrine\ORM\EntityManagerInterface;
use JetBrains\PhpStorm\Pure;

class PurchaseManager
{
    public EntityManagerInterface $manager;

    public function __construct(EntityManagerInterface $managerController)
    {
        $this->manager = $managerController;
    }

    public function persist(Purchase $purchase): void
    {
        if ($this->verifyDisponibilityProduct($purchase)) {
            $this->removeProductQuantity($purchase);

            $this->manager->persist($purchase);
            $this->manager->flush();
        }
    }

    /**
     * @param Purchase $purchase
     * @return void
     * Remove the purchase <br>
     * If the ordered linked contains only 1 purchase, we remove the ordered too
     */
    public function remove(Purchase $purchase): void
    {
        $orderedRepository = $this->manager->getRepository(Ordered::class);
        $purchaseRepository = $this->manager->getRepository(Purchase::class);
        $orderedManager = new OrderedManager($this->manager);

        $ordered = $orderedRepository->find($purchase->getOrdered());
        $purchaseLinked = $purchaseRepository->findBy(["ordered" => $ordered]);

        $this->manager->remove($purchase);
        if (sizeof($purchaseLinked) == 1){
            $orderedManager->remove($ordered);
        }

        $this->manager->flush();
    }

    public function removeWithRestore(Purchase $purchase): void
    {
        $product = $purchase->getProduct();
        $productManager = new ProductManager($this->manager);

        //Restock le produit de la quantity
        $productManager->restockProduct($product, $purchase->getQuantity());

        $this->manager->remove($purchase);
        $this->manager->flush();
    }

    #[Pure]
    public function verifyDisponibilityProduct(Purchase $purchase): bool
    {
        $product = $purchase->getProduct();

        return ($product->getQuantityStock() >= $purchase->getQuantity());
    }

    /**
     * @param Purchase $purchase
     * @return void
     * Remove the quantity of the product ordered
     */
    public function removeProductQuantity(Purchase $purchase)
    {
        $product = $purchase->getProduct();
        $product->setQuantityStock($product->getQuantityStock() - $purchase->getQuantity());
    }
}