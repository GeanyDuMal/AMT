<?php

namespace App\Entity;

use App\Repository\PurchaseRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=PurchaseRepository::class)
 */
class Purchase {
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private int $id;

    /**
     * @ORM\ManyToOne(targetEntity=Product::class)
     * @ORM\JoinColumn(onDelete="CASCADE", nullable=false)
     */
    private Product $product;

    /**
     * @ORM\Column(type="integer")
     */
    private int $quantity;

    /**
     * @ORM\ManyToOne(targetEntity=Ordered::class, inversedBy="purchases")
     * @ORM\JoinColumn(nullable=false, onDelete="CASCADE")
     */
    private Ordered $ordered;

    /**
     * @ORM\Column(type="decimal", precision=5, scale=2)
     */
    private float $unitaryPrice;

    public function getId(): int {
        return $this->id;
    }

    public function getProduct(): Product {
        return $this->product;
    }

    public function setProduct(Product $product): self {
        $this->product = $product;

        return $this;
    }

    public function getQuantity(): int {
        return $this->quantity;
    }

    public function setQuantity(int $quantity): self {
        $this->quantity = $quantity;

        return $this;
    }

    public function getOrdered(): Ordered {
        return $this->ordered;
    }

    public function setOrdered(Ordered $ordered): self {
        $this->ordered = $ordered;

        return $this;
    }

    public function getUnitaryPrice(): float {
        return $this->unitaryPrice;
    }

    public function setUnitaryPrice(float $unitaryPrice): self {
        $this->unitaryPrice = $unitaryPrice;

        return $this;
    }
}
