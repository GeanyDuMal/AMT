<?php

namespace App\Manager;

use App\Entity\Post;
use App\Repository\PostRepository;
use App\Utils\Enum\PostType;
use App\Utils\PictureUtils;
use App\Utils\RandomUtils;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;

class PostManager
{
    public EntityManagerInterface $manager;
    public PostRepository $postRepository;

    public function __construct(EntityManagerInterface $entityManager) {
        $this->manager = $entityManager;
        $this->postRepository = $this->manager->getRepository(Post::class);
    }

    public function persist(Post $post): void {
        if ($this->verifyPost($post)) {
            $this->replaceImageIfEmpty($post);

            if (!$post->getCreationDate()) {
                $post->setCreationDate(new DateTime('now'));
            }

            $this->manager->persist($post);
            $this->manager->flush();
        }
    }

    public function remove(Post $post): void {
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
     * @param DateTime $creationDate
     * @param string $imageLink
     * @return void
     */
    public function setData(Post $post, string $postType, string $postTitle, string $postDescription,
        DateTime $creationDate, string $imageLink = ""): void {
        $post->setTitle($postTitle);
        $post->setDescription($postDescription);
        $post->setPostType($postType);
        $post->setCreationDate($creationDate);
        $post->setImageLink($imageLink);
    }

    /**
     * @param Post $post
     * @return void
     */
    public function replaceImageIfEmpty(Post $post): void {
        if ($post->getImageLink() == "") {
            $post->setImageLink('/img/entity/placeholder.png');
        }
    }

    /**
     * @param string $pictureLink
     * @param Post $post
     */
    public function downloadPicture(string $pictureLink, Post $post): void {
        $pictureUtils = new PictureUtils();
        $randomUtils = new RandomUtils();
        $characters = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
        $postExist = (bool)$this->postRepository->findOneBy(["title" => $post->getTitle(),
                                                             "postType" => $post->getPostType()]);
        $idUsed = 1;


        if (!$postExist) {
            $lastPost = $this->postRepository->findOneBy([], ["id" => "DESC"]);

            if ($lastPost) {
                $idUsed = $lastPost->getId() + 1;
            }
        } else {
            $idUsed = $post->getId();
        }

        if (($post->getImageLink() != (null || "")) && !str_contains($post->getImageLink(), "placeholder")) {
            $pictureUtils->deletePicture($post->getImageLink());
        }

        $newLocation = "/img/entity/post/img_" . $idUsed . "_" . $randomUtils->randomString(4, $characters) . ".png";

        $post->setImageLink($pictureUtils->downloadPicture($pictureLink, $newLocation));
    }
}