<?php

namespace App\DataFixtures;

use App\Entity\AssociationRole;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AssociationRoleFixture extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $associationRole = new AssociationRole();
        $associationRole->setName("President");
        $manager->persist($associationRole);

        $associationRole = new AssociationRole();
        $associationRole->setName("Tresorier");
        $manager->persist($associationRole);

        $associationRole = new AssociationRole();
        $associationRole->setName("Vice President");
        $manager->persist($associationRole);

        $associationRole = new AssociationRole();
        $associationRole->setName("Secretaire");
        $manager->persist($associationRole);

        $associationRole = new AssociationRole();
        $associationRole->setName("Membre");
        $manager->persist($associationRole);

        $manager->flush();

    }
}
