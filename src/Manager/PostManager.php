<?php

namespace App\Manager;

use App\Entity\Post;
use App\Utils\Enum\PostType;
use DateTime;
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

    public function persist(Post $post): void
    {
        if ($this->verifyPost($post)) {
            $this->replaceImageIfEmpty($post);

            if (!$post->getCreationDate()){
                $post->setCreationDate(new DateTime('now'));
            }

            $this->manager->persist($post);
            $this->manager->flush();
        }
    }

    public function remove(Post $post): void
    {
        $this->manager->remove($post);
        $this->manager->flush();
    }

    public function verifyPost(Post $post): bool {
        return (
            in_array($post->getPostType(), PostType::getAll()) &&
            $post->getTitle() != "" &&
            $post->getDescription() != ""
        );
    }

    /**
     * @param Post $post
     * @return bool
     */
    public function verifyPost(Post $post): bool {
        return (
            in_array($post->getPostType(), PostType::getAll()) &&
            $post->getTitle() != "" &&
            $post->getDescription() != ""
        );
    }

    /**
     * @param Post $post
     * @param string $postType
     * @param String $postTitle
     * @param String $postDescription
     * @param string|null $imageLink
     * @return void
     */
    public function setData(Post   $post, string $postType, string $postTitle,
                            string $postDescription, ?string $imageLink): void
    {
        $post->setTitle($postTitle);
        $post->setDescription($postDescription);
        $post->setImageLink($imageLink);
        $post->setPostType($postType);
    }

    /**
     * @param Post $post
     * @return void
     */
    public function replaceImageIfEmpty(Post $post): void
    {
        if ($post->getImageLink() == "") {
            $post->setImageLink('https://a2mo-197c6.kxcdn.com/wp-content/uploads/2021/10/placeholder1.png');
        }
    }
}