<?php

namespace App\DataFixtures;

use App\Entity\Client;
use App\Entity\ClientType;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class ClientFixture extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $clientTypeRepository = $manager->getRepository(ClientType::class);
        $clientRepository = $manager->getRepository(Client::class);

        $client1 = new Client();
        $client1->setName("NATANELIC")
                ->setFirstName("Romain")
                ->setLogin("log123")
                ->setPassword("hiddenPassword")
                ->setBalance(0)
                ->setFidelityPoint(0)
                ->setClientType($clientTypeRepository->findOneBy(["name" => "Association"]))
                ->setRoles(["ROLE_PRESIDENT"]);
        $manager->persist($client1);

        $client2 = new Client();
        $client2->setName("MULLER")
                ->setFirstName("Leane")
                ->setLogin("monLogin")
                ->setPassword("Ayato")
                ->setBalance(5)
                ->setFidelityPoint(0)
                ->setClientType($clientTypeRepository->findOneBy(["name" => "Association"]))
                ->setRoles(["ROLE_ASSOC"]);
        $manager->persist($client2);

        $client3 = new Client();
        $client3->setName("LASALLE")
                ->setFirstName("Jean")
                ->setLogin("grospif")
                ->setPassword("petiteCochonne")
                ->setBalance(0)
                ->setFidelityPoint(0)
                ->setClientType($clientTypeRepository->findOneBy(["name" => "Etudiant"]))
                ->setRoles(["ROLE_USER"]);
        $manager->persist($client3);

        $manager->flush();

        //Permet de mettre les bon roles
        $listClient = $clientRepository->findAll();

        $client1 = $listClient[0];
        $client1->setClientType($clientTypeRepository->findOneBy(["name" => "Association"]));
        $manager->persist($client1);

        $client2 = $listClient[1];
        $client2->setClientType($clientTypeRepository->findOneBy(["name" => "Association"]));
        $manager->persist($client2);

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return[
            ClientTypeFixture::class
        ];
    }
}
