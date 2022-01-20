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
                ->setQuantityStock(10)
                ->setImageLink("https://www.mashed.com/img/gallery/the-untold-truth-of-snickers/intro-1587489779.jpg");
        $manager->persist($product1);

        $product2 = new Product();
        $product2->setName("M&Ms")
            ->setProductType($productTypeRepository->findOneBy(["name" => "Snack"]))
            ->setQuantityStock(10)
            ->setImageLink("https://www.top-bonbon.com/566-large_default/m-m-s-bonbon-mars.jpg");
        $manager->persist($product2);

        $product3 = new Product();
        $product3->setName("Chips")
            ->setProductType($productTypeRepository->findOneBy(["name" => "Snack"]))
            ->setQuantityStock(10)
            ->setImageLink("https://www.adjovan.com/wp-content/uploads/2020/02/1-2020-02-17T142640.275.jpg");
        $manager->persist($product3);

        $product4 = new Product();
        $product4->setName("Oreo")
            ->setProductType($productTypeRepository->findOneBy(["name" => "Snack"]))
            ->setQuantityStock(10)
            ->setImageLink("https://i.makeagif.com/media/5-14-2020/KHA2dt.gif");
        $manager->persist($product4);

        $product5 = new Product();
        $product5->setName("Coca Cherry")
                ->setProductType($productTypeRepository->findOneBy(["name" => "Boisson"]))
                ->setQuantityStock(10)
                ->setImageLink("https://cazapizz.fr/wp-content/uploads/2018/11/coca-cherry-33cl.jpg");
        $manager->persist($product5);

        $product6 = new Product();
        $product6->setName("Pepsi Max")
            ->setProductType($productTypeRepository->findOneBy(["name" => "Boisson"]))
            ->setQuantityStock(10)
            ->setImageLink("https://faistoilivrer.fr/514-home_default/pepsi-33cl-.jpg");
        $manager->persist($product6);

        $product7 = new Product();
        $product7->setName("Fuze Tea")
            ->setProductType($productTypeRepository->findOneBy(["name" => "Boisson"]))
            ->setQuantityStock(10)
            ->setImageLink("https://www.valgourmand.com/16749/fuze-tea-peche-boite-33cl.jpg");
        $manager->persist($product7);

        $product8 = new Product();
        $product8->setName("Minute Maid Orange")
            ->setProductType($productTypeRepository->findOneBy(["name" => "Boisson"]))
            ->setQuantityStock(10)
            ->setImageLink("https://st-west.fr/wp-content/uploads/2020/05/canette-minute-maid-scaled.jpg");
        $manager->persist($product8);

        $product9 = new Product();
        $product9->setName("Capri Sun MultiVitamine")
            ->setProductType($productTypeRepository->findOneBy(["name" => "Boisson"]))
            ->setQuantityStock(10)
            ->setImageLink("https://www.cdiscount.com/pdt2/5/6/2/4/550x550/cap2009937911562/rw/capri-sonne-multivitamines-10-x-0-2l.jpg");
        $manager->persist($product9);

        $product10 = new Product();
        $product10->setName("Capri Sun Tropical")
            ->setProductType($productTypeRepository->findOneBy(["name" => "Boisson"]))
            ->setQuantityStock(10)
            ->setImageLink("https://cdn.monoprix.fr/cdn-cgi/image/width=580,quality=60,format=auto,metadata=none/assets/images/grocery/2561914/580x580.jpg");
        $manager->persist($product10);

        $product11 = new Product();
        $product11->setName("Eau")
            ->setProductType($productTypeRepository->findOneBy(["name" => "Boisson"]))
            ->setQuantityStock(10)
            ->setImageLink("https://static-retail.bewaps.com/data/121/produits/40420-3-large.jpg");
        $manager->persist($product11);

        $manager->flush();
    }

    public function getDependencies()
    {
        return[
            ProductTypeFixture::class
        ];
    }
}
