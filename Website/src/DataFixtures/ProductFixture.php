<?php

namespace App\DataFixtures;

use App\Entity\Product;
use App\Entity\ProductType;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class ProductFixture extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $productTypeRepository = $manager->getRepository(ProductType::class);

        $product1 = new Product();
        $product1->setName("Snickers")
                ->setProductType($productTypeRepository->findOneBy(["name" => "Snack"]))
                ->setQuantityStock(5)
                ->setImageLink("http://placehold.it/200x200");
        $manager->persist($product1);

        $product2 = new Product();
        $product2->setName("Coca Cherry")
                ->setProductType($productTypeRepository->findOneBy(["name" => "Boisson"]))
                ->setQuantityStock(10)
                ->setImageLink("http://placehold.it/200x200");
        $manager->persist($product2);

        $manager->flush();
    }

    public function getDependencies()
    {
        return[
            ProductTypeFixture::class
        ];
    }
}
