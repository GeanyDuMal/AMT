<?php

namespace App\Manager;

use App\Entity\Post;
use App\Entity\PostType;
use App\Repository\PostRepository;
use App\Repository\PostTypeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;

class PostManager
{
    public EntityManagerInterface $manager;
    public PostRepository $postRepository;

    public function __construct(EntityManagerInterface $managerController)
    {
        $this->manager = $managerController;
        $this->postRepository = (PostRepository::class)($this->manager->getRepository(Post::class));
    }

    /**
     * @param Post $post
     * @param PostType $postType
     * @param String $postTitle
     * @param String $postDescription
     * @param String $imageLink
     * @return void
     */
    public function setData(Post $post, PostType $postType, String $postTitle,
                            String $postDescription, String $imageLink){
        $post->setTitle($postTitle);
        $post->setDescription($postDescription);
        $post->setImageLink($imageLink);
        $post->setPostType($postType);
    }
}