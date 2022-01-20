<?php

namespace App\DataFixtures;

use App\Entity\Client;
use App\Entity\Ordered;
use App\Entity\Purchase;
use App\Entity\Product;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class PurchaseFixture extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $orderRepository = $manager->getRepository(Ordered::class);
        $productRepository = $manager->getRepository(Product::class);

        $allOrder = $orderRepository->findAll();

        //--------------------------------
        //Order 1

        $purchase = new Purchase();
        $purchase->setOrdered($allOrder[0])
                ->setProduct($productRepository->findOneBy(["name" => "Snickers"]))
                ->setQuantity(2);
        $manager->persist($purchase);

        $purchase = new Purchase();
        $purchase->setOrdered($allOrder[0])
            ->setProduct($productRepository->findOneBy(["name" => "Fuze tea"]))
            ->setQuantity(1);
        $manager->persist($purchase);

        //--------------------------------
        //Order 2

        $purchase = new Purchase();
        $purchase->setOrdered($allOrder[1])
            ->setProduct($productRepository->findOneBy(["name" => "M&Ms"]))
            ->setQuantity(1);
        $manager->persist($purchase);

        //--------------------------------
        //Order 3

        $purchase = new Purchase();
        $purchase->setOrdered($allOrder[2])
            ->setProduct($productRepository->findOneBy(["name" => "Oreo"]))
            ->setQuantity(3);
        $manager->persist($purchase);

        $purchase = new Purchase();
        $purchase->setOrdered($allOrder[2])
            ->setProduct($productRepository->findOneBy(["name" => "Coca Cherry"]))
            ->setQuantity(1);
        $manager->persist($purchase);

        //--------------------------------
        //Order 4

        $purchase = new Purchase();
        $purchase->setOrdered($allOrder[3])
            ->setProduct($productRepository->findOneBy(["name" => "Chips"]))
            ->setQuantity(1);
        $manager->persist($purchase);

        $purchase = new Purchase();
        $purchase->setOrdered($allOrder[3])
            ->setProduct($productRepository->findOneBy(["name" => "Oreo"]))
            ->setQuantity(1);
        $manager->persist($purchase);

        $purchase = new Purchase();
        $purchase->setOrdered($allOrder[3])
            ->setProduct($productRepository->findOneBy(["name" => "CapriSun Tropical"]))
            ->setQuantity(1);
        $manager->persist($purchase);

        //--------------------------------

        $manager->flush();
    }

    public function getDependencies()
    {
        return[
            OrderedFixture::class,
            ProductFixture::class
        ];
    }
}
