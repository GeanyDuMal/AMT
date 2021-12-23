<?php

namespace App\DataFixtures;

use App\Entity\Association;
use App\Entity\AssociationRole;
use App\Entity\Client;
use App\Entity\ClientType;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class AssociationFixture extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $clientRepository = $manager->getRepository(Client::class);
        $clientTypeRepository = $manager->getRepository(ClientType::class);
        $associationRoleRepository = $manager->getRepository(AssociationRole::class);

        $membreAssoc = $clientRepository->findBy(["clientType" => $clientTypeRepository->findOneBy(["name" => "Association"])]);

        dd($clientTypeRepository->findOneBy(["name" => "Association"]),
            $clientRepository->findBy(["clientType" => $clientTypeRepository->findOneBy(["name" => "Association"])]),
            $clientRepository->findAll(),
            $membreAssoc);

        $association1 = new Association();
        $association1->setMember($membreAssoc[0])
                    ->setRole($associationRoleRepository->findOneBy(["name" => "President"]));
        $manager->persist($association1);

        $association2 = new Association();
        $association2->setMember($membreAssoc[1])
                    ->setRole($associationRoleRepository->findOneBy(["name" => "Membre"]));
        $manager->persist($association2);
        
        $manager->flush();
    }

    public function getDependencies()
    {
        return[
            ClientFixture::class,
            AssociationRoleFixture::class
        ];
    }
}
