<?php

namespace App\DataFixtures;

use App\Entity\Association;
use App\Entity\Client;
use App\Utils\Enum\AssociationRole;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class AssociationFixture extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $clientRepository = $manager->getRepository(Client::class);


        $association = new Association();
        $association->setMember($clientRepository->findOneBy(["login" => "RomainGamer57"]))
            ->setRole(AssociationRole::PRESIDENT);
        $manager->persist($association);

        $association = new Association();
        $association->setMember($clientRepository->findOneBy(["login" => "Dhoulnoun"]))
            ->setRole(AssociationRole::TRESORIER);
        $manager->persist($association);

        $association = new Association();
        $association->setMember($clientRepository->findOneBy(["login" => "Omareee"]))
            ->setRole(AssociationRole::SECRETAIRE);
        $manager->persist($association);

        $association = new Association();
        $association->setMember($clientRepository->findOneBy(["login" => "LeaneLoli"]))
            ->setRole(AssociationRole::MEMBRE);
        $manager->persist($association);

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            ClientFixture::class,
        ];
    }
}
