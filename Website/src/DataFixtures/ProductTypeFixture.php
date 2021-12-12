<?php

namespace App\DataFixtures;

use App\Entity\ProductType;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class ProductTypeFixture extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        /** Effectué dans la migration car ce sont des types qui sont obligatoires

        $productType1 = new ProductType();
        $productType1->setName("Snack");
        $manager->persist($productType1);

        $productType2 = new ProductType();
        $productType2->setName("Boisson");
        $manager->persist($productType2);

        $manager->flush();

        */
    }
}