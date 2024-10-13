<?php

namespace App\DataFixtures;

use App\Entity\Member;
use App\Entity\Client;
use App\Utils\Enum\MemberRoleEnum;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class MemberFixture extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $clientRepository = $manager->getRepository(Client::class);

        $member = new Member();
        $member->setClient($clientRepository->findOneBy(["login" => "RomainGamer57"]))
            ->setRole(MemberRoleEnum::PRESIDENT);
        $manager->persist($member);

        $member = new Member();
        $member->setClient($clientRepository->findOneBy(["login" => "Dhoulnoun"]))
            ->setRole(MemberRoleEnum::TRESORIER);
        $manager->persist($member);

        $member = new Member();
        $member->setClient($clientRepository->findOneBy(["login" => "Omareee"]))
            ->setRole(MemberRoleEnum::SECRETAIRE);
        $manager->persist($member);

        $member = new Member();
        $member->setClient($clientRepository->findOneBy(["login" => "LeaneLoli"]))
            ->setRole(MemberRoleEnum::MEMBRE);
        $manager->persist($member);

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            ClientFixture::class,
        ];
    }
}
