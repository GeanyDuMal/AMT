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
    public $manager;

    public function __construct(EntityManagerInterface $managerController)
    {
        $this->manager = $managerController;
    }

    /**
     * @param $purchaseList
     *
     *
     * @return void
     */

    /**
     * @param $purchaseList [productId => quantity]
     * @param Client|null $client Client
     * @return PaymentType $paymentTypeList[]
     */
    public function getAllowedPaymentType($purchaseList, Client $client = null): PaymentType
    {
        $clientTypeRepository = $this->manager->getRepository(ClientType::class);
        if ($client != null) {
            $clientType = $client->getClientType();
        }else{
            $clientType = $clientTypeRepository->findOneBy(["name" => "Etudiant"]);
        }

        $montant = 0;
        $productRepository = $this->manager->getRepository(Product::class);
        $paymentTypeRepository = $this->manager->getRepository(PaymentType::class);
        $priceRepository = $this->manager->getRepository(Price::class);

        foreach ($purchaseList as $productId => $quantity){
            $product = $productRepository->find($productId);
            $montant = $montant + $priceRepository->findOneBy(["product" => $product, "clientType" => $clientType])
                    ->getPrice() * $quantity;
        }

        $paymentTypeList = $paymentTypeRepository->findAll();
        foreach ($paymentTypeList as $paymentType){
            switch ($paymentType->getName()){
                case "Solde" :{
                    if ($client == null || $client->getBalance() < $montant){
                        unset($paymentTypeList[array_search($paymentType, $paymentTypeList, true)]);
                    }
                    break;
                }
                case "Carte Bancaire" :{
                    if ($montant < 1){
                        unset($paymentTypeList[array_search($paymentType, $paymentTypeList, true)]);
                    }
                    break;
                }
            }
        }
        return $paymentTypeList;
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