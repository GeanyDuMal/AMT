<?php

namespace App\DataFixtures;

use App\Entity\Ordered;
use App\Entity\Client;
use App\Entity\PaymentType;
use DateTime;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class OrderedFixture extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $clientRepository = $manager->getRepository(Client::class);
        $paymentTypeRepository = $manager->getRepository(PaymentType::class);

        $paymentTypeCarte = $paymentTypeRepository->findOneBy(["name" => "Carte Bancaire"]);
        $paymentTypeEspece = $paymentTypeRepository->findOneBy(["name" => "Espece"]);
        $paymentTypeSolde = $paymentTypeRepository->findOneBy(["name" => "Solde"]);
        $dateNow = new DateTime("now");

        $order = new Ordered();
        $order->setClient($clientRepository->findOneBy(["name" => "NATANELIC"]))
                ->setPaymentType($paymentTypeCarte)
                ->setOrderedAt($dateNow);
        $manager->persist($order);

        $order = new Ordered();
        $order->setClient($clientRepository->findOneBy(["name" => "MULLER"]))
                ->setPaymentType($paymentTypeEspece)
                ->setOrderedAt($dateNow);
        $manager->persist($order);

        $order = new Ordered();
        $order->setClient($clientRepository->findOneBy(["name" => "GHONIEM"]))
            ->setPaymentType($paymentTypeSolde)
            ->setOrderedAt($dateNow);
        $manager->persist($order);

        $order = new Ordered();
        $order->setClient($clientRepository->findOneBy(["name" => "TIJOU"]))
            ->setPaymentType($paymentTypeCarte)
            ->setOrderedAt($dateNow);
        $manager->persist($order);

        $order = new Ordered();
        $order->setClient(null)
            ->setPaymentType($paymentTypeEspece)
            ->setOrderedAt($dateNow);
        $manager->persist($order);

        $manager->flush();
    }

    public function getDependencies()
    {
        return[
            PaymentTypeFixture::class,
            ClientFixture::class
        ];
    }
}
