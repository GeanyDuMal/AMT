<?php

namespace App\DataFixtures;

use App\Entity\ClientType;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class ClientTypeFixture extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $clientType = new ClientType();
        $clientType->setName("Association");
        $manager->persist($clientType);
        
        $clientType = new ClientType();
        $clientType->setName("Etudiant");
        $manager->persist($clientType);

        $manager->flush();

    }
}
