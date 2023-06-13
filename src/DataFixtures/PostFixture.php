<?php

namespace App\DataFixtures;

use App\Entity\Post;
use App\Utils\Enum\PostType;
use DateTime;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class PostFixture extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $postTypeEvent = PostType::EVENT;
        $postTypeAutre = PostType::AUTRE;


        $post = new Post();
        $post->setTitle("Tournoi Smash")
            ->setPostType($postTypeEvent)
            ->setDescription("Le tournoi de Smash aura lieu le 19 Janvier 2022 en salle F06, merci de passer 
                            au bureau afin de vous inscrire pour pouvoir participer")
            ->setImageLink("https://images.smash.gg/images/tournament/297869/image-458b78f5c942da129c7a2f38beb19299.jpg")
            ->setCreationDate(new DateTime("now"));
        $manager->persist($post);

        $post = new Post();
        $post->setTitle("Retrait SweatShirt")
            ->setPostType($postTypeAutre)
            ->setDescription("Les Sweats et les Tshirts sont enfin arrivé au bureau, pensez a venir les recuperer
                                        afin que vous puissiez les revetir ;-)")
            ->setImageLink("https://media.dior.com/couture/ecommerce/media/catalog/product/i/H/1604511903_113J698A0531_C989_E01_GHC.jpg?imwidth=800")
            ->setCreationDate(new DateTime("now"));
        $manager->persist($post);

        $post = new Post();
        $post->setTitle("Veste Oubliée")
            ->setPostType($postTypeAutre)
            ->setDescription("Une veste a été oublié au bureau, merci de venir la recupérer")
            ->setImageLink("https://assets.laboutiqueofficielle.com/w_450,q_auto,f_auto/media/products/2021/03/02/mtx_255225_TEDDY-497_BLACK-WHITE_20210309T164443_01.jpg")
            ->setCreationDate(new DateTime("now"));
        $manager->persist($post);

        $manager->flush();
    }
}
