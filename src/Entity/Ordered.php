<?php

namespace App\Entity;

use App\Repository\OrderedRepository;
use App\Utils\Enum\ClientTypeEnum;
use App\Utils\Enum\OrderedStatusEnum;
use App\Utils\Enum\PaymentTypeEnum;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=OrderedRepository::class)
 */
class Ordered {
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
     * @ORM\Column(type="string", enumType=ClientTypeEnum::class)
     */
    private ClientTypeEnum $clientTypeAtOrder;

    /**
     * @ORM\Column(type="string", enumType=PaymentTypeEnum::class, nullable=false)
     */
    private PaymentTypeEnum $paymentType;

    /**
     * @ORM\OneToMany(targetEntity=Purchase::class, mappedBy="ordered", orphanRemoval=true, cascade={"persist", "remove"})
     */
    private Collection $purchases;

    /**
     * @ORM\Column(type="string", enumType=OrderedStatusEnum::class)
     */
    private OrderedStatusEnum $status;

    public function __construct() {
        $this->purchases = new ArrayCollection();
    }

    public function getId(): int {
        return $this->id;
    }

    public function getOrderedAt(): DateTime {
        return $this->orderedAt;
    }

    public function setOrderedAt(DateTime $orderedAt): self {
        $this->orderedAt = $orderedAt;

        return $this;
    }

    public function getClient(): ?Client {
        return $this->client;
    }

    public function setClient(?Client $client): self {
        $this->client = $client;

        return $this;
    }

    public function getClientTypeAtOrder(): ClientTypeEnum {
        return $this->clientTypeAtOrder;
    }

    public function setClientTypeAtOrder(ClientTypeEnum $clientTypeAtOrder): self {
        $this->clientTypeAtOrder = $clientTypeAtOrder;

        return $this;
    }

    public function getPaymentType(): PaymentTypeEnum {
        return $this->paymentType;
    }

    public function setPaymentType(PaymentTypeEnum $paymentType): self {
        $this->paymentType = $paymentType;

        return $this;
    }

    /**
     * @return Collection<int, Purchase>
     */
    public function getPurchases(): Collection {
        return $this->purchases;
    }

    public function addPurchase(Purchase $purchase): self {
        if (!$this->purchases->contains($purchase)) {
            $this->purchases[] = $purchase;
            $purchase->setOrdered($this);
        }

        return $this;
    }

    /**
     * @param Collection $purchases
     * @return $this
     */
    public function setPurchases(Collection $purchases): self {
        $this->purchases = $purchases;

        foreach ($this->purchases as $purchase) {
            $purchase->setOrdered($this);
        }

        return $this;
    }

    public function getStatus(): OrderedStatusEnum {
        return $this->status;
    }

    public function setStatus(OrderedStatusEnum $status): self {
        $this->status = $status;

        return $this;
    }
}
