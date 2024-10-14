<?php

namespace App\DataFixtures;

use App\Entity\Client;
use App\Entity\Ordered;
use App\Utils\Enum\ClientTypeEnum;
use App\Utils\Enum\OrderedStatus;
use App\Utils\Enum\PaymentTypeEnum;
use DateTime;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class OrderedFixture extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $clientRepository = $manager->getRepository(Client::class);

        $paymentTypeCarte = PaymentTypeEnum::CARTE_BANCAIRE;
        $paymentTypeEspece = PaymentTypeEnum::ESPECE;
        $paymentTypeSolde = PaymentTypeEnum::SOLDE;

        $clientTypeEtudiant = ClientTypeEnum::ETUDIANT;
        $clientTypeCotisant = ClientTypeEnum::COTISANT;
        $clientTypeAssociation = ClientTypeEnum::ASSOCIATION;

        $orderedStatusPaid = OrderedStatus::PAID;
        $orderedStatusCanceled = OrderedStatus::CANCELED;
        $orderedStatusRefunded = OrderedStatus::REFUNDED;
        $orderedStatusWaitingPayment = OrderedStatus::WAITING_PAYMENT;

        $dateNow = new DateTime("now");

        $order = new Ordered();
        $order->setClient($clientRepository->findOneBy(["name" => "NATANELIC"]))
            ->setPaymentType($paymentTypeCarte)
            ->setOrderedAt($dateNow)
            ->setClientTypeAtOrder($clientTypeAssociation)
            ->setStatus($orderedStatusCanceled);
        $manager->persist($order);

        $order = new Ordered();
        $order->setClient($clientRepository->findOneBy(["name" => "MULLER"]))
            ->setPaymentType($paymentTypeEspece)
            ->setOrderedAt($dateNow)
            ->setClientTypeAtOrder($clientTypeCotisant)
            ->setStatus($orderedStatusPaid);
        $manager->persist($order);

        $order = new Ordered();
        $order->setClient($clientRepository->findOneBy(["name" => "GHONIEM"]))
            ->setPaymentType($paymentTypeSolde)
            ->setOrderedAt($dateNow)
            ->setClientTypeAtOrder($clientTypeCotisant)
            ->setStatus($orderedStatusPaid);
        $manager->persist($order);

        $order = new Ordered();
        $order->setClient($clientRepository->findOneBy(["name" => "TIJOU"]))
            ->setPaymentType($paymentTypeCarte)
            ->setOrderedAt($dateNow)
            ->setClientTypeAtOrder($clientTypeEtudiant)
            ->setStatus($orderedStatusRefunded);
        $manager->persist($order);

        $order = new Ordered();
        $order->setClient(null)
            ->setPaymentType($paymentTypeEspece)
            ->setOrderedAt($dateNow)
            ->setClientTypeAtOrder($clientTypeEtudiant)
            ->setStatus($orderedStatusWaitingPayment);
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
