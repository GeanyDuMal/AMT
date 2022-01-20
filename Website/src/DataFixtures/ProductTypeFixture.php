<?php

namespace App\DataFixtures;

use App\Entity\ProductType;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class ProductTypeFixture extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $productType = new ProductType();
        $productType->setName("Snack");
        $manager->persist($productType);

        $productType = new ProductType();
        $productType->setName("Boisson");
        $manager->persist($productType);

        $manager->flush();

    }
}