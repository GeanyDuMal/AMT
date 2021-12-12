<?php

namespace App\DataFixtures;

use App\Entity\PaymentType;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class PaymentTypeFixture extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        /** Effectué dans la migration car ce sont des types qui sont obligatoires

        $paymentType1 = new PaymentType();
        $paymentType1->setName("Carte Bancaire");
        $manager->persist($paymentType1);

        $paymentType2 = new PaymentType();
        $paymentType2->setName("Espece");
        $manager->persist($paymentType2);

        $paymentType3 = new PaymentType();
        $paymentType3->setName("Solde");
        $manager->persist($paymentType3);

        $manager->flush();

        */
    }
}
