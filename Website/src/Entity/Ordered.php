<?php

namespace App\Entity;

use App\Repository\OrderedRepository;
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
    private $id;

    /**
     * @ORM\Column(type="datetime")
     */
    private ?\DateTime $orderedAt;

    /**
     * @ORM\ManyToOne(targetEntity=Client::class)
     * @ORM\JoinColumn(nullable=true)
     */
    private ?Client $client;

    /**
     * @ORM\ManyToOne(targetEntity=PaymentType::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private ?PaymentType $paymentType;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getOrderedAt(): ?\DateTime
    {
        return $this->orderedAt;
    }

    public function setOrderedAt(\DateTime $orderedAt): self
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

    public function getPaymentType(): ?PaymentType
    {
        return $this->paymentType;
    }

    public function setPaymentType(?PaymentType $paymentType): self
    {
        $this->paymentType = $paymentType;

        return $this;
    }
}
