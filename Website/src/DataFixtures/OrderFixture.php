<?php

namespace App\DataFixtures;

use App\Entity\Order;
use App\Entity\Client;
use App\Entity\PaymentType;
use DateTime;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class OrderFixture extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $clientRepository = $manager->getRepository(Client::class);
        $paymentTypeRepository = $manager->getRepository(PaymentType::class);

        $order1 = new Order();
        $order1->setClient($clientRepository->findOneBy(["name" => "NATANELIC"]))
            ->setPaymentType($paymentTypeRepository->findOneBy(["name" => "Carte Bancaire"]))
            ->setOrderedAt(new DateTime());

        $manager->persist($order1);

        $order2 = new Order();
        $order2->setClient($clientRepository->findOneBy(["name" => "MULLER"]))
            ->setPaymentType($paymentTypeRepository->findOneBy(["name" => "Espece"]))
            ->setOrderedAt(new DateTime());
        $manager->persist($order2);

        dd();

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
