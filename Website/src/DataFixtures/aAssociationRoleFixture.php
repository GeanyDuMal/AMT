<?php

namespace App\DataFixtures;

use App\Entity\AssociationRole;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class aAssociationRoleFixture extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $associationRole1 = new AssociationRole();
        $associationRole1->setName("President");
        $manager->persist($associationRole1);

        $associationRole2 = new AssociationRole();
        $associationRole2->setName("Tresorier");
        $manager->persist($associationRole2);

        $associationRole3 = new AssociationRole();
        $associationRole3->setName("Vice President");
        $manager->persist($associationRole3);

        $associationRole4 = new AssociationRole();
        $associationRole4->setName("Secretaire");
        $manager->persist($associationRole4);

        $associationRole5 = new AssociationRole();
        $associationRole5->setName("Membre");
        $manager->persist($associationRole5);

        $manager->flush();
    }

    public function getDependencies()
    {
        // TODO: Implement getDependencies() method.
    }
}
