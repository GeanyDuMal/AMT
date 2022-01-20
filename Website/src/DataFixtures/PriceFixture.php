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

        $clientTypeEtudiant = $clientTypeRepository->findOneBy(["name" => "Etudiant"]);
        $clientTypeAssociation = $clientTypeRepository->findOneBy(["name" => "Association"]);


        $snickers = $productRepository->findOneBy(["name" => "Snickers"]);
        $price1 = new Price();
        $price1->setClientType($clientTypeAssociation)
                ->setProduct($snickers)
                ->setPrice(0.6);
        $manager->persist($price1);

        $price2 = new Price();
        $price2->setClientType($clientTypeEtudiant)
                ->setProduct($snickers)
                ->setPrice(0.8);
        $manager->persist($price2);

        $mms = $productRepository->findOneBy(["name" => "M&Ms"]);
        $price3 = new Price();
        $price3->setClientType($clientTypeAssociation)
                ->setProduct($mms)
                ->setPrice(0.6);
        $manager->persist($price3);

        $price4 = new Price();
        $price4->setClientType($clientTypeEtudiant)
                ->setProduct($mms)
                ->setPrice(0.8);
        $manager->persist($price4);

        $chips = $productRepository->findOneBy(["name" => "Chips"]);
        $price5 = new Price();
        $price5->setClientType($clientTypeAssociation)
                ->setProduct($chips)
                ->setPrice(0.6);
        $manager->persist($price5);

        $price6 = new Price();
        $price6->setClientType($clientTypeEtudiant)
                ->setProduct($chips)
                ->setPrice(0.8);
        $manager->persist($price6);

        $oreo = $productRepository->findOneBy(["name" => "Oreo"]);
        $price7 = new Price();
        $price7->setClientType($clientTypeAssociation)
                ->setProduct($oreo)
                ->setPrice(0.4);
        $manager->persist($price7);

        $price8 = new Price();
        $price8->setClientType($clientTypeEtudiant)
                ->setProduct($oreo)
                ->setPrice(0.5);
        $manager->persist($price8);

        $colaCherry = $productRepository->findOneBy(["name" => "Coca Cherry"]);
        $price9 = new Price();
        $price9->setClientType($clientTypeAssociation)
                ->setProduct($colaCherry)
                ->setPrice(0.6);
        $manager->persist($price9);

        $price10 = new Price();
        $price10->setClientType($clientTypeEtudiant)
                ->setProduct($colaCherry)
                ->setPrice(0.8);
        $manager->persist($price10);

        $pepsiMax = $productRepository->findOneBy(["name" => "Pepsi Max"]);
        $price11 = new Price();
        $price11->setClientType($clientTypeAssociation)
            ->setProduct($pepsiMax)
            ->setPrice(0.6);
        $manager->persist($price11);

        $price12 = new Price();
        $price12->setClientType($clientTypeEtudiant)
            ->setProduct($pepsiMax)
            ->setPrice(0.8);
        $manager->persist($price12);

        $fuzeTea = $productRepository->findOneBy(["name" => "Fuze Tea"]);
        $price13 = new Price();
        $price13->setClientType($clientTypeAssociation)
            ->setProduct($fuzeTea)
            ->setPrice(0.6);
        $manager->persist($price13);

        $price14 = new Price();
        $price14->setClientType($clientTypeEtudiant)
            ->setProduct($fuzeTea)
            ->setPrice(0.8);
        $manager->persist($price14);

        $minuteMaidOrange = $productRepository->findOneBy(["name" => "Minute Maid Orange"]);
        $price15 = new Price();
        $price15->setClientType($clientTypeAssociation)
            ->setProduct($minuteMaidOrange)
            ->setPrice(0.6);
        $manager->persist($price15);

        $price16 = new Price();
        $price16->setClientType($clientTypeEtudiant)
            ->setProduct($minuteMaidOrange)
            ->setPrice(0.8);
        $manager->persist($price16);

        $capriSunMulti = $productRepository->findOneBy(["name" => "Capri Sun MultiVitamine"]);
        $price17 = new Price();
        $price17->setClientType($clientTypeAssociation)
            ->setProduct($capriSunMulti)
            ->setPrice(0.6);
        $manager->persist($price17);

        $price18 = new Price();
        $price18->setClientType($clientTypeEtudiant)
            ->setProduct($capriSunMulti)
            ->setPrice(0.8);
        $manager->persist($price18);

        $capriSunTropi = $productRepository->findOneBy(["name" => "Capri Sun Tropical"]);
        $price19 = new Price();
        $price19->setClientType($clientTypeAssociation)
            ->setProduct($capriSunTropi)
            ->setPrice(0.6);
        $manager->persist($price19);

        $price20 = new Price();
        $price20->setClientType($clientTypeEtudiant)
            ->setProduct($capriSunTropi)
            ->setPrice(0.8);
        $manager->persist($price20);

        $eau = $productRepository->findOneBy(["name" => "Eau"]);
        $price21 = new Price();
        $price21->setClientType($clientTypeAssociation)
            ->setProduct($eau)
            ->setPrice(0.3);
        $manager->persist($price21);

        $price22 = new Price();
        $price22->setClientType($clientTypeEtudiant)
            ->setProduct($eau)
            ->setPrice(0.3);
        $manager->persist($price22);

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
