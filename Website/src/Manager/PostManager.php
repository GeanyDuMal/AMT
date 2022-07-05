<?php

namespace App\Manager;

use App\Entity\Post;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectRepository;
use JetBrains\PhpStorm\Pure;

class PostManager
{
    public EntityManagerInterface $manager;
    public ObjectRepository $postRepository;

    public function __construct(EntityManagerInterface $managerController)
    {
        $this->manager = $managerController;
        $this->postRepository = $this->manager->getRepository(Post::class);
    }

    public function persist(Post $post)
    {
        if ($this->verifPost($post)) {
            $this->replaceImageIfEmpty($post);

            $this->manager->persist($post);
            $this->manager->flush();
        }
    }

    /**
     * @param Post $post
     * @param string $postType
     * @param String $postTitle
     * @param String $postDescription
     * @param String $imageLink
     * @return void
     */
    public function setData(Post   $post, string $postType, string $postTitle,
                            string $postDescription, string $imageLink): void
    {
        $post->setTitle($postTitle);
        $post->setDescription($postDescription);
        $post->setImageLink($imageLink);
        $post->setPostType($postType);
    }


    #[Pure]
    public function verifPost(Post $post): bool
    {
        return ($post->getTitle() != "" && $post->getDescription() != "");
    }

    public function replaceImageIfEmpty(Post $post): void
    {
        if ($post->getImageLink() == null || $post->getImageLink() == "") {
            $post->setImageLink('https://a2mo-197c6.kxcdn.com/wp-content/uploads/2021/10/placeholder1.png');
        }
    }
}