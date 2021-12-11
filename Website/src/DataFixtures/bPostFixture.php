<?php

namespace App\DataFixtures;

use App\Entity\Post;
use App\Entity\PostType;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class bPostFixture extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $postTypeRepository = $manager->getRepository(PostType::class);

        $post1 = new Post();
        $post1->setTitle("Tournoi Smash")
            ->setPostType($postTypeRepository->findOneBy(["name" => "Event"]))
            ->setDescription("Ceci est la description de cet evenement")
            ->setImageLink("http://placehold.it/200x200");
        $manager->persist($post1);
        
        $post2 = new Post();
        $post2->setTitle("Debut de la vente des sweats")
            ->setPostType($postTypeRepository->findOneBy(["name" => "Autre"]))
            ->setDescription("Ceci est la description de cet evenement")
            ->setImageLink("http://placehold.it/200x200");
        $manager->persist($post2);

        $manager->flush();
    }
}
