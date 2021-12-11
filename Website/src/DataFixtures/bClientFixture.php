<?php

namespace App\DataFixtures;

use App\Entity\Client;
use App\Entity\ClientType;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class bClientFixture extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $clientTypeRepository = $manager->getRepository(ClientType::class);

        $client1 = new Client();
        $client1->setName("NATANELIC")
                ->setFirstName("Romain")
                ->setLogin("log123")
                ->setPassword("hiddenPassword")
                ->setBalance(0)
                ->setFidelityPoint(0)
                ->setClientType($clientTypeRepository->findOneBy(["name" => "Association"]));
        $manager->persist($client1);

        $client2 = new Client();
        $client2->setName("MULLER")
                ->setFirstName("Leane")
                ->setLogin("monLogin")
                ->setPassword("Ayato")
                ->setBalance(5)
                ->setFidelityPoint(0)
                ->setClientType($clientTypeRepository->findOneBy(["name" => "Association"]));
        $manager->persist($client2);

        $client3 = new Client();
        $client3->setName("LASALLE")
                ->setFirstName("Jean")
                ->setLogin("grospif")
                ->setPassword("petiteCochonne")
                ->setBalance(0)
                ->setFidelityPoint(0)
                ->setClientType($clientTypeRepository->findOneBy(["name" => "Etudiant"]));
        $manager->persist($client3);

        $manager->flush();
    }
}
