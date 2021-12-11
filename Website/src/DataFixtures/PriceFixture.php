<?php

namespace App\DataFixtures;

use App\Entity\ClientType;
use App\Entity\Price;
use App\Entity\Product;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class PriceFixture extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $clientTypeRepository = $manager->getRepository(ClientType::class);
        $productRepository = $manager->getRepository(Product::class);

        $allProduct = $productRepository->findAll();
        $allClient = $clientTypeRepository->findAll();

        $price1 = new Price();
        $price1->setClientType($allClient[0])
                ->setProduct($allProduct[0])
                ->setPrice(0.6);
        $manager->persist($price1);

        $price2 = new Price();
        $price2->setClientType($allClient[1])
                ->setProduct($allProduct[0])
                ->setPrice(0.8);
        $manager->persist($price2);

        $price3 = new Price();
        $price3->setClientType($allClient[0])
                ->setProduct($allProduct[1])
                ->setPrice(0.6);
        $manager->persist($price3);

        $price4 = new Price();
        $price4->setClientType($allClient[1])
                ->setProduct($allProduct[1])
                ->setPrice(0.8);
        $manager->persist($price4);

        $manager->flush();
    }

    public function getDependencies()
    {
        return[
            ClientTypeFixture::class,
            ProductFixture::class
        ];
    }
}
