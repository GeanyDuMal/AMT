<?php

namespace App\DataFixtures;

use App\Entity\PostType;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class PostTypeFixture extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $postType1 = new PostType();
        $postType1->setName("Event");
        $manager->persist($postType1);

        $postType2 = new PostType();
        $postType2->setName("Autre");
        $manager->persist($postType2);

        $manager->flush();
    }
}
