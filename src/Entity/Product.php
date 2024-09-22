<?php

namespace App\Entity;

use App\Repository\ProductRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=ProductRepository::class)
 */
class Product
{
    /**
     * @Groups("product")
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private int $id;

    /**
     * @Groups("product")
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="Le produit doit avoir un nom")
     */
    private string $name;

    /**
     * @Groups("product")
     * @ORM\Column(type="string", length=255)
     */
    private string $productType;

    /**
     * @Groups("product")
     * @ORM\Column(type="integer")
     * @Assert\PositiveOrZero(message="La quantité doit etre positif ou null")
     */
    private int $quantityStock;

    /**
     * @Groups("product")
     * @ORM\Column(type="string", length=255)
     */
    private ?string $imageLink;

    /**
     * @Groups("product")
     * @ORM\OneToMany(targetEntity=Price::class, mappedBy="product", orphanRemoval=true, fetch="EAGER")
     */
    private Collection $prices;

    /**
     * @ORM\Column(type="boolean", options={"default": true})
     */
    private ?bool $active = null;

    public function __construct()
    {
        $this->prices = new ArrayCollection();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getProductType(): string
    {
        return $this->productType;
    }

    public function setProductType(string $productType): self
    {
        $this->productType = $productType;

        return $this;
    }

    public function getQuantityStock(): int
    {
        return $this->quantityStock;
    }

    public function setQuantityStock(int $quantityStock): self
    {
        $this->quantityStock = $quantityStock;

        return $this;
    }

    public function getImageLink(): string
    {
        return $this->imageLink;
    }

    public function setImageLink(string $imageLink): self
    {
        $this->imageLink = $imageLink;

        return $this;
    }

    /**
     * @return Collection
     */
    public function getPrices(): Collection
    {
        return $this->prices;
    }

    public function addPrice(Price $price): self
    {
        if (!$this->prices->contains($price)) {
            $this->prices[] = $price;
            $price->setProduct($this);
        }

        return $this;
    }

    public function removePrice(Price $price): self
    {
        if ($this->prices->removeElement($price)) {
            // set the owning side to null (unless already changed)
            if ($price->getProduct() === $this) {
                $price->setProduct(null);
            }
        }

        return $this;
    }

    public function isActive(): ?bool
    {
        return $this->active;
    }

    public function setActive(bool $active): self
    {
        $this->active = $active;

        return $this;
    }
}
