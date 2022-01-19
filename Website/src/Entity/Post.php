<?php

namespace App\Entity;

use App\Repository\PostRepository;
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
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $title;

    /**
     * @ORM\Column(type="text")
     */
    private $description;

    /**
     * @ORM\Column(type="string", length=255, options={"default":"https://a2mo-197c6.kxcdn.com/wp-content/uploads/2021/10/placeholder1.png"})
     * @ORM\Column(nullable=true)
     */
    private $imageLink;

    /**
     * @ORM\ManyToOne(targetEntity=PostType::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private $postType;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getImageLink(): ?string
    {
        return $this->imageLink;
    }

    public function setImageLink(string $imageLink): self
    {
        $this->imageLink = $imageLink;

        return $this;
    }

    public function getPostType(): ?PostType
    {
        return $this->postType;
    }

    public function setPostType(?PostType $postType): self
    {
        $this->postType = $postType;

        return $this;
    }
}
