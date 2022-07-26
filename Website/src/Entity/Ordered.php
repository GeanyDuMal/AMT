<?php

namespace App\Entity;

use App\Repository\OrderedRepository;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=OrderedRepository::class)
 */
class Ordered
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private int $id;

    /**
     * @ORM\Column(type="datetime")
     */
    private DateTime $orderedAt;

    /**
     * @ORM\ManyToOne(targetEntity=Client::class)
     * @ORM\JoinColumn(nullable=true, onDelete="SET NULL")
     */
    private ?Client $client;

    /**
     * @ORM\Column(type="string")
     */
    private string $paymentType;

    /**
     * @ORM\OneToMany(targetEntity=Purchase::class, mappedBy="ordered", orphanRemoval=true)
     */
    private Collection $purchases ;

    public function __construct()
    {
        $this->purchases = new ArrayCollection();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getOrderedAt(): DateTime
    {
        return $this->orderedAt;
    }

    public function setOrderedAt(DateTime $orderedAt): self
    {
        $this->orderedAt = $orderedAt;

        return $this;
    }

    public function getClient(): ?Client
    {
        return $this->client;
    }

    public function setClient(?Client $client): self
    {
        $this->client = $client;

        return $this;
    }

    public function getPaymentType(): string
    {
        return $this->paymentType;
    }

    public function setPaymentType(string $paymentType): self
    {
        $this->paymentType = $paymentType;

        return $this;
    }

    /**
     * @return Collection<int, Purchase>
     */
    public function getPurchases(): Collection
    {
        return $this->purchases;
    }

    public function addPurchase(Purchase $purchase): self
    {
        if (!$this->purchases->contains($purchase)) {
            $this->purchases[] = $purchase;
            $purchase->setOrdered($this);
        }

        return $this;
    }

    public function removePurchase(Purchase $purchase): self
    {
        if ($this->purchases->removeElement($purchase)) {
            // set the owning side to null (unless already changed)
            if ($purchase->getOrdered() === $this) {
                $purchase->setOrdered(null);
            }
        }

        return $this;
    }
}
