<?php

namespace App\DataFixtures;

use App\Entity\Client;
use App\Entity\PasswordForgotRequest;
use App\Entity\Post;
use App\Utils\Enum\PostType;
use DateTime;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class PasswordForgotRequestFixture extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $clientRepository = $manager->getRepository(Client::class);
        $dateNow = new DateTime("now");

        $passwordRequest1 = new PasswordForgotRequest();
        $passwordRequest1->setClient($clientRepository->findOneBy(["name" => "NATANELIC"]));
        $passwordRequest1->setConfirmationCode("1254896");
        $passwordRequest1->setDate($dateNow);
        $manager->persist($passwordRequest1);

        $passwordRequest2 = new PasswordForgotRequest();
        $passwordRequest2->setClient($clientRepository->findOneBy(["name" => "MULLER"]));
        $passwordRequest2->setConfirmationCode("azertyuiop");
        $passwordRequest2->setDate($dateNow);
        $manager->persist($passwordRequest2);

        $passwordRequest3 = new PasswordForgotRequest();
        $passwordRequest3->setClient($clientRepository->findOneBy(["name" => "GHONIEM"]));
        $passwordRequest3->setConfirmationCode("11223344");
        $passwordRequest3->setDate($dateNow);
        $manager->persist($passwordRequest3);

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            ClientFixture::class
        ];
    }
}
