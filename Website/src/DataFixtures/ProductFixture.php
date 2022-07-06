<?php

namespace App\DataFixtures;

use App\Entity\Product;
use App\Utils\Enum\ProductType;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class ProductFixture extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $productTypeBoisson = ProductType::BOISSON;
        $productTypeSnack = ProductType::SNACK;


        $product = new Product();
        $product->setName("Snickers")
            ->setProductType($productTypeSnack)
            ->setQuantityStock(10)
            ->setImageLink("https://www.mashed.com/img/gallery/the-untold-truth-of-snickers/intro-1587489779.jpg");
        $manager->persist($product);

        $product = new Product();
        $product->setName("M&Ms")
            ->setProductType($productTypeSnack)
            ->setQuantityStock(10)
            ->setImageLink("https://www.top-bonbon.com/566-large_default/m-m-s-bonbon-mars.jpg");
        $manager->persist($product);

        $product = new Product();
        $product->setName("Chips")
            ->setProductType($productTypeSnack)
            ->setQuantityStock(10)
            ->setImageLink("https://www.adjovan.com/wp-content/uploads/2020/02/1-2020-02-17T142640.275.jpg");
        $manager->persist($product);

        $product = new Product();
        $product->setName("Oreo")
            ->setProductType($productTypeSnack)
            ->setQuantityStock(10)
            ->setImageLink("https://i.gifer.com/7H8I.gif");
        $manager->persist($product);

        $product = new Product();
        $product->setName("Coca Cherry")
            ->setProductType($productTypeBoisson)
            ->setQuantityStock(10)
            ->setImageLink("https://cazapizz.fr/wp-content/uploads/2018/11/coca-cherry-33cl.jpg");
        $manager->persist($product);

        $product = new Product();
        $product->setName("Pepsi Max")
            ->setProductType($productTypeBoisson)
            ->setQuantityStock(10)
            ->setImageLink("https://faistoilivrer.fr/514-home_default/pepsi-33cl-.jpg");
        $manager->persist($product);

        $product = new Product();
        $product->setName("Fuze Tea")
            ->setProductType($productTypeBoisson)
            ->setQuantityStock(10)
            ->setImageLink("https://www.valgourmand.com/16749/fuze-tea-peche-boite-33cl.jpg");
        $manager->persist($product);

        $product = new Product();
        $product->setName("Minute Maid Orange")
            ->setProductType($productTypeBoisson)
            ->setQuantityStock(10)
            ->setImageLink("https://st-west.fr/wp-content/uploads/2020/05/canette-minute-maid-scaled.jpg");
        $manager->persist($product);

        $product = new Product();
        $product->setName("CapriSun MultiVitamine")
            ->setProductType($productTypeBoisson)
            ->setQuantityStock(0)
            ->setImageLink("https://www.cdiscount.com/pdt2/5/6/2/4/550x550/cap2009937911562/rw/capri-sonne-multivitamines-10-x-0-2l.jpg");
        $manager->persist($product);

        $product = new Product();
        $product->setName("CapriSun Tropical")
            ->setProductType($productTypeBoisson)
            ->setQuantityStock(10)
            ->setImageLink("https://cdn.monoprix.fr/cdn-cgi/image/width=580,quality=60,format=auto,metadata=none/assets/images/grocery/2561914/580x580.jpg");
        $manager->persist($product);

        $product = new Product();
        $product->setName("Eau")
            ->setProductType($productTypeBoisson)
            ->setQuantityStock(10)
            ->setImageLink("https://static-retail.bewaps.com/data/121/produits/40420-3-large.jpg");
        $manager->persist($product);

        $manager->flush();
    }
}
