<?php

namespace App\DataFixtures;

use App\Entity\PostType;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class PostTypeFixture extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $postType = new PostType();
        $postType->setName("Event");
        $manager->persist($postType);

        $postType = new PostType();
        $postType->setName("Autre");
        $manager->persist($postType);

        $manager->flush();
    }
}
