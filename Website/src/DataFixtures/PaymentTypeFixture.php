<?php

namespace App\DataFixtures;

use App\Entity\PaymentType;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class PaymentTypeFixture extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $paymentType = new PaymentType();
        $paymentType->setName("Carte Bancaire");
        $manager->persist($paymentType);

        $paymentType = new PaymentType();
        $paymentType->setName("Espece");
        $manager->persist($paymentType);

        $paymentType = new PaymentType();
        $paymentType->setName("Solde");
        $manager->persist($paymentType);

        $manager->flush();
    }
}
