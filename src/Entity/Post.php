<?php

namespace App\Entity;

use App\Repository\PostRepository;
use App\Utils\Enum\PostTypeEnum;
use DateTime;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=PostRepository::class)
 */
class Post
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private int $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private string $title;

    /**
     * @ORM\Column(type="text")
     */
    private string $description;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private string $imageLink;

    /**
     * @ORM\Column(type="string", enumType=PostTypeEnum::class, length=255)
     */
    private PostTypeEnum $postType;

    /**
     * @ORM\Column(type="date")
     */
    private DateTime $creationDate;

    public function getId(): int {
        return $this->id;
    }

    public function getTitle(): string {
        return $this->title;
    }

    public function setTitle(string $title): self {
        $this->title = $title;

        return $this;
    }

    public function getDescription(): string {
        return $this->description;
    }

    public function setDescription(string $description): self {
        $this->description = $description;

        return $this;
    }

    public function getImageLink(): string {
        return $this->imageLink;
    }

    public function setImageLink(string $imageLink): self {
        $this->imageLink = $imageLink;

        return $this;
    }

    public function getPostType(): PostTypeEnum {
        return $this->postType;
    }

    public function setPostType(PostTypeEnum $postType): self {
        $this->postType = $postType;

        return $this;
    }

    public function getCreationDate(): DateTime {
        return $this->creationDate;
    }

    public function setCreationDate(DateTime $creationDate): self {
        $this->creationDate = $creationDate;

        return $this;
    }

    public function equals(Post $post): bool {
        return (
            $this->getTitle() === $post->getTitle() &&
            $this->getDescription() === $post->getDescription() &&
            $this->getImageLink() === $post->getImageLink() &&
            $this->getPostType() === $post->getPostType() &&
            $this->getCreationDate() === $post->getCreationDate()
        );
    }
}