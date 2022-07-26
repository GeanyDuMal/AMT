<?php

namespace App\Entity;

use App\Repository\OrderedRepository;
use DateTime;
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
     * @ORM\ManyToOne(targetEntity = Client::class)
     * @ORM\JoinColumn(nullable=true, onDelete="SET NULL")
     */
    private ?Client $client;

    /**
     * @ORM\Column(type="string", nullable=false)
     */
    private string $paymentType;

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
}
