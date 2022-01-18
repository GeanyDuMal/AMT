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
        $commandRepository = $manager->getRepository(Ordered::class);
        $productRepository = $manager->getRepository(Product::class);
        $clientRepository = $manager ->getRepository(Client::class);

        $purchase = new Purchase();
        $purchase->setCommand($commandRepository->findOneBy(["client" => $clientRepository->findOneBy(["name" => "NATANELIC"])]))
                ->setProduct($productRepository->findOneBy(["name" => "Snickers"]))
                ->setQuantity(2);
        $manager->persist($purchase);

        $purchase2 = new Purchase();
        $purchase2->setCommand($commandRepository->findOneBy(["client" => $clientRepository->findOneBy(["name" => "NATANELIC"])]))
                ->setProduct($productRepository->findOneBy(["name" => "Coca Cherry"]))
                ->setQuantity(1);
        $manager->persist($purchase2);

        $purchase3 = new Purchase();
        $purchase3->setCommand($commandRepository->findOneBy(["client" => $clientRepository->findOneBy(["name" => "MULLER"])]))
                ->setProduct($productRepository->findOneBy(["name" => "Snickers"]))
                ->setQuantity(3);
        $manager->persist($purchase3);

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
