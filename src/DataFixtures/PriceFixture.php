<?php

namespace App\DataFixtures;

use App\Entity\Price;
use App\Entity\Product;
use App\Utils\Enum\ClientTypeEnum;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class PriceFixture extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $productRepository = $manager->getRepository(Product::class);

        $clientTypeAssociation = ClientTypeEnum::ASSOCIATION;
        $clientTypeEtudiant = ClientTypeEnum::ETUDIANT;


        $snickers = $productRepository->findOneBy(["name" => "Snickers"]);
        $price = new Price();
        $price->setClientType($clientTypeAssociation)
                ->setProduct($snickers)
                ->setPrice(0.6);
        $manager->persist($price);

        $price = new Price();
        $price->setClientType($clientTypeEtudiant)
                ->setProduct($snickers)
                ->setPrice(0.8);
        $manager->persist($price);

        $mms = $productRepository->findOneBy(["name" => "M&Ms"]);
        $price = new Price();
        $price->setClientType($clientTypeAssociation)
                ->setProduct($mms)
                ->setPrice(0.6);
        $manager->persist($price);

        $price = new Price();
        $price->setClientType($clientTypeEtudiant)
                ->setProduct($mms)
                ->setPrice(0.8);
        $manager->persist($price);

        $chips = $productRepository->findOneBy(["name" => "Chips"]);
        $price = new Price();
        $price->setClientType($clientTypeAssociation)
                ->setProduct($chips)
                ->setPrice(0.6);
        $manager->persist($price);

        $price = new Price();
        $price->setClientType($clientTypeEtudiant)
                ->setProduct($chips)
                ->setPrice(0.8);
        $manager->persist($price);

        $oreo = $productRepository->findOneBy(["name" => "Oreo"]);
        $price = new Price();
        $price->setClientType($clientTypeAssociation)
                ->setProduct($oreo)
                ->setPrice(0.4);
        $manager->persist($price);

        $price = new Price();
        $price->setClientType($clientTypeEtudiant)
                ->setProduct($oreo)
                ->setPrice(0.5);
        $manager->persist($price);

        $colaCherry = $productRepository->findOneBy(["name" => "Coca Cherry"]);
        $price = new Price();
        $price->setClientType($clientTypeAssociation)
                ->setProduct($colaCherry)
                ->setPrice(0.6);
        $manager->persist($price);

        $price = new Price();
        $price->setClientType($clientTypeEtudiant)
                ->setProduct($colaCherry)
                ->setPrice(0.8);
        $manager->persist($price);

        $pepsiMax = $productRepository->findOneBy(["name" => "Pepsi Max"]);
        $price = new Price();
        $price->setClientType($clientTypeAssociation)
            ->setProduct($pepsiMax)
            ->setPrice(0.6);
        $manager->persist($price);

        $price = new Price();
        $price->setClientType($clientTypeEtudiant)
            ->setProduct($pepsiMax)
            ->setPrice(0.8);
        $manager->persist($price);

        $fuzeTea = $productRepository->findOneBy(["name" => "Fuze Tea"]);
        $price = new Price();
        $price->setClientType($clientTypeAssociation)
            ->setProduct($fuzeTea)
            ->setPrice(0.6);
        $manager->persist($price);

        $price = new Price();
        $price->setClientType($clientTypeEtudiant)
            ->setProduct($fuzeTea)
            ->setPrice(0.8);
        $manager->persist($price);

        $minuteMaidOrange = $productRepository->findOneBy(["name" => "Minute Maid Orange"]);
        $price = new Price();
        $price->setClientType($clientTypeAssociation)
            ->setProduct($minuteMaidOrange)
            ->setPrice(0.6);
        $manager->persist($price);

        $price = new Price();
        $price->setClientType($clientTypeEtudiant)
            ->setProduct($minuteMaidOrange)
            ->setPrice(0.8);
        $manager->persist($price);

        $capriSunMulti = $productRepository->findOneBy(["name" => "CapriSun MultiVitamine"]);
        $price = new Price();
        $price->setClientType($clientTypeAssociation)
            ->setProduct($capriSunMulti)
            ->setPrice(0.6);
        $manager->persist($price);

        $price = new Price();
        $price->setClientType($clientTypeEtudiant)
            ->setProduct($capriSunMulti)
            ->setPrice(0.8);
        $manager->persist($price);

        $capriSunTropi = $productRepository->findOneBy(["name" => "CapriSun Tropical"]);
        $price = new Price();
        $price->setClientType($clientTypeAssociation)
            ->setProduct($capriSunTropi)
            ->setPrice(0.6);
        $manager->persist($price);

        $price = new Price();
        $price->setClientType($clientTypeEtudiant)
            ->setProduct($capriSunTropi)
            ->setPrice(0.8);
        $manager->persist($price);

        $eau = $productRepository->findOneBy(["name" => "Eau"]);
        $price = new Price();
        $price->setClientType($clientTypeAssociation)
            ->setProduct($eau)
            ->setPrice(0.3);
        $manager->persist($price);

        $price = new Price();
        $price->setClientType($clientTypeEtudiant)
            ->setProduct($eau)
            ->setPrice(0.3);
        $manager->persist($price);

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return[
            ProductFixture::class
        ];
    }
}
