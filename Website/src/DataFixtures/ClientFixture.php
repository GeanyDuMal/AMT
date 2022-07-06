<?php

namespace App\DataFixtures;

use App\Entity\Client;
use App\Utils\Enum\ClientType;
use App\Utils\Enum\SymfonyRole;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class ClientFixture extends Fixture
{
    public function load(ObjectManager $manager): void
    {

        $clientTypeAssociation = ClientType::ASSOCIATION;
        $clientTypeEtudiant = ClientType::ETUDIANT;

        $client = new Client();
        $client->setName("NATANELIC")
                ->setFirstName("Romain")
                ->setLogin("RomainGamer57")
                ->setPassword("hiddenPassword")
                ->setBalance(1.50)
                ->setFidelityPoint(26)
                ->setClientType($clientTypeAssociation)
                ->setRoles([SymfonyRole::ROLE_PRESIDENT]);
        $manager->persist($client);

        $client = new Client();
        $client->setName("MULLER")
                ->setFirstName("Leane")
                ->setLogin("LeaneLoli")
                ->setPassword("Ayato")
                ->setBalance(5)
                ->setFidelityPoint(12)
                ->setClientType($clientTypeAssociation)
                ->setRoles([SymfonyRole::ASSOC]);
        $manager->persist($client);

        $client = new Client();
        $client->setName("GHONIEM")
            ->setFirstName("Younes")
            ->setLogin("Dhoulnoun")
            ->setPassword("Ijamami")
            ->setBalance(0)
            ->setFidelityPoint(60)
            ->setClientType($clientTypeAssociation)
            ->setRoles([SymfonyRole::TRESORIER]);
        $manager->persist($client);

        $client = new Client();
        $client->setName("ELLOUMI")
            ->setFirstName("Omar")
            ->setLogin("Omareee")
            ->setPassword("ChorbaDev")
            ->setBalance(80)
            ->setFidelityPoint(0)
            ->setClientType($clientTypeAssociation)
            ->setRoles([SymfonyRole::ASSOC]);
        $manager->persist($client);

        $client = new Client();
        $client->setName("TIJOU")
            ->setFirstName("Allan")
            ->setLogin("Xamp2012")
            ->setPassword("Password")
            ->setBalance(0)
            ->setFidelityPoint(0)
            ->setClientType($clientTypeEtudiant)
            ->setRoles([SymfonyRole::USER]);
        $manager->persist($client);

        $manager->flush();
    }
}
