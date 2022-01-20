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
        $associationRoleRepository = $manager->getRepository(AssociationRole::class);


        $association = new Association();
        $association->setMember($clientRepository->findOneBy(["login" => "RomainGamer57"]))
                    ->setRole($associationRoleRepository->findOneBy(["name" => "President"]));
        $manager->persist($association);

        $association = new Association();
        $association->setMember($clientRepository->findOneBy(["login" => "Dhoulnoun"]))
            ->setRole($associationRoleRepository->findOneBy(["name" => "Tresorier"]));
        $manager->persist($association);

        $association = new Association();
        $association->setMember($clientRepository->findOneBy(["login" => "Omareee"]))
            ->setRole($associationRoleRepository->findOneBy(["name" => "Secretaire"]));
        $manager->persist($association);

        $association = new Association();
        $association->setMember($clientRepository->findOneBy(["login" => "LeaneLoli"]))
            ->setRole($associationRoleRepository->findOneBy(["name" => "Membre"]));
        $manager->persist($association);
        
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
