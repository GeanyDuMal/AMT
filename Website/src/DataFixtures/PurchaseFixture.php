<?php

namespace App\DataFixtures;

use App\Entity\Client;
use App\Entity\Purchase;
use App\Entity\Order;
use App\Entity\Product;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class PurchaseFixture extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $orderRepository = $manager->getRepository(Order::class);
        $productRepository = $manager->getRepository(Product::class);
        $clientRepository = $manager ->getRepository(Client::class);

        $purchase = new Purchase();
        $purchase->setOrder($orderRepository->findOneBy(["client" => $clientRepository->findOneBy(["name" => "NATANELIC"])]))
                ->setProduct($productRepository->findOneBy(["name" => "Snickers"]))
                ->setQuantity(2);
        $manager->persist($purchase);

        $purchase2 = new Purchase();
        $purchase2->setOrder($orderRepository->findOneBy(["client" => $clientRepository->findOneBy(["name" => "NATANELIC"])]))
                ->setProduct($productRepository->findOneBy(["name" => "Coca Cherry"]))
                ->setQuantity(1);
        $manager->persist($purchase2);

        $purchase3 = new Purchase();
        $purchase3->setOrder($orderRepository->findOneBy(["client" => $clientRepository->findOneBy(["name" => "MULLER"])]))
                ->setProduct($productRepository->findOneBy(["name" => "Snickers"]))
                ->setQuantity(3);
        $manager->persist($purchase3);

        $manager->flush();
    }

    public function getDependencies()
    {
        return[
            OrderFixture::class,
            ProductFixture::class
        ];
    }
}
