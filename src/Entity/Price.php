<?php

namespace App\Entity;

use App\Repository\PriceRepository;
use App\Utils\Enum\ClientTypeEnum;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=PriceRepository::class)
 */
class Price
{
    /**
     * @Groups("price_product")
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity=Product::class, inversedBy="prices")
     * @ORM\JoinColumn(nullable=false, onDelete="CASCADE")
     * @Assert\NotNull(message="Le produit dans prix ne doit pas etre null")
     */
    private Product $product;

    /**
     * @Groups("price")
     * @ORM\Id
     * @ORM\Column(type="string", enumType=ClientTypeEnum::class)
     * @Assert\NotNull(message="Le type client dans prix ne doit pas etre null")
     */
    private ClientTypeEnum $clientType;

    /**
     * @Groups("price")
     * @ORM\Column(type="decimal", precision=5, scale=2)
     * @Assert\Positive(message="Le prix doit etre positif")
     */
    private string $price;

    public function getProduct(): Product
    {
        return $this->product;
    }

    public function setProduct(Product $product): self
    {
        $this->product = $product;

        return $this;
    }

    public function getClientType(): ClientTypeEnum
    {
        return $this->clientType;
    }

    public function setClientType(ClientTypeEnum $clientType): self
    {
        $this->clientType = $clientType;

        return $this;
    }

    public function getPrice(): string
    {
        return $this->price;
    }

    public function setPrice(string $price): self
    {
        $this->price = $price;

        return $this;
    }
}
