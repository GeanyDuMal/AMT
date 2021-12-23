<?php

namespace App\DataFixtures;

use App\Entity\Command;
use App\Entity\Client;
use App\Entity\PaymentType;
use DateTime;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class CommandFixture extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $clientRepository = $manager->getRepository(Client::class);

        $paymentTypeRepository = $manager->getRepository(PaymentType::class);

        $command1 = new Command();
        $command1->setClient($clientRepository->findOneBy(["name" => "NATANELIC"]))
                ->setPaymentType($paymentTypeRepository->findOneBy(["name" => "Carte Bancaire"]))
                ->setOrderedAt(new DateTime("now"));
        $manager->persist($command1);

        $command2 = new Command();
        $command2->setClient($clientRepository->findOneBy(["name" => "MULLER"]))
                ->setPaymentType($paymentTypeRepository->findOneBy(["name" => "Espece"]))
                ->setOrderedAt(new DateTime("now"));
        $manager->persist($command2);

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
