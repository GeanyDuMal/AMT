<?php

namespace App\DataFixtures;

use App\Entity\Client;
use App\Entity\Ordered;
use App\Utils\Enum\PaymentType;
use DateTime;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class OrderedFixture extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $clientRepository = $manager->getRepository(Client::class);

        $paymentTypeCarte = PaymentType::CARTE_BANCAIRE;
        $paymentTypeEspece = PaymentType::ESPECE;
        $paymentTypeSolde = PaymentType::SOLDE;
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

    public function getDependencies(): array
    {
        return [
            ClientFixture::class
        ];
    }
}
