<?php

namespace App\DataFixtures;

use App\Entity\ClientType;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class ClientTypeFixture extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $clientType1 = new ClientType();
        $clientType1->setName("Association");
        $manager->persist($clientType1);
        
        $clientType2 = new ClientType();
        $clientType2->setName("Etudiant");
        $manager->persist($clientType2);

        $manager->flush();

    }
}
