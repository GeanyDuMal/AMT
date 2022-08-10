<?php

namespace App\Manager;

use App\Entity\Post;
use App\Utils\Enum\PostType;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectRepository;
use Exception;

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
        $pictureUtils = new PictureUtils();
        $pictureUtils->deletePicture($post->getImageLink());

        $this->manager->remove($post);
        $this->manager->flush();
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
     * @param string $imageLink
     * @return void
     */
    public function setData(Post   $post, string $postType, string $postTitle,
                            string $postDescription, string $imageLink, DateTime $creationDate): void
    {
        $post->setTitle($postTitle);
        $post->setDescription($postDescription);
        $post->setImageLink($imageLink);
        $post->setPostType($postType);
        $post->setCreationDate($creationDate);
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

    /**
     * @param string $link
     * @return string The link where the picture is stored
     */
    public function downloadPicture(string $link): string
    {
        $lastPost = $this->postRepository->findOneBy([], ["id" => "DESC"]);
        $newId = 1;

        if ($lastPost){
            $newId = $lastPost->getId()+1;
        }

        $location = "/img/entity/post/img_".$newId.".png";

        $pictureUtils = new PictureUtils();

        return $pictureUtils->downloadPicture($link, $location);
    }
}