<?php

namespace App\Entity;

use App\Repository\PriceRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
/**
 * @ORM\Entity(repositoryClass=PriceRepository::class)
 */
class Price
{
    /**
     * @ORM\Id 
     * @ORM\ManyToOne(targetEntity=Product::class)
     * @ORM\JoinColumn(nullable=false, onDelete="CASCADE" )
     * @Assert\NotNull(message="Le produit dans prix ne doit pas etre null")
     */
    private $product;

    /**
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity=ClientType::class)
     * @ORM\JoinColumn(nullable=false)
     * @Assert\NotNull(message="Le type client dans prix ne doit pas etre null")
     */
    private $clientType;

    /**
     * @ORM\Column(type="decimal", precision=5, scale=2)
     * @Assert\Positive(message="Le prix doit etre positif")
     */
    private $price;

    public function getProduct(): ?Product
    {
        return $this->product;
    }

    public function setProduct(?Product $product): self
    {
        $this->product = $product;

        return $this;
    }

    public function getClientType(): ?ClientType
    {
        return $this->clientType;
    }

    public function setClientType(?ClientType $clientType): self
    {
        $this->clientType = $clientType;

        return $this;
    }

    public function getPrice(): ?string
    {
        return $this->price;
    }

    public function setPrice(string $price): self
    {
        $this->price = $price;

        return $this;
    }
}
