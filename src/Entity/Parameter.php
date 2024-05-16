<?php

namespace App\Entity;

use App\Repository\ParameterRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ParameterRepository::class)
 */
class Parameter
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
    private ?String $associationName = null;

    /**
     * @ORM\Column(type="string", length=1023)
     */
    private ?String $associationDescription = null;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private ?string $linkLogo = null;

    /**
     * @ORM\Column(type="integer")
     */
    private ?int $amountFidelityPointToExchange = null;

    /**
     * @ORM\Column(type="decimal", precision=5, scale=2)
     */
    private ?string $amountBalanceToAddAfterExchange = null;

    /**
     * @ORM\Column(type="boolean")
     */
    private ?bool $cotisantActivated = null;

    /**
     * @ORM\Column(type="boolean")
     */
    private ?bool $postActivated = null;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private ?string $linkHomeImage = null;

    public function getId(): ?int {
        return $this->id;
    }

    public function getLinkLogo(): ?string {
        return $this->linkLogo;
    }

    public function setLinkLogo(?string $linkLogo): self {
        $this->linkLogo = $linkLogo;

        return $this;
    }

    public function getLinkHomeImage(): ?string {
        return $this->linkHomeImage;
    }

    public function setLinkHomeImage(?string $linkHomeImage): self {
        $this->linkHomeImage = $linkHomeImage;

        return $this;
    }

    public function getAssociationName(): ?string {
        return $this->associationName;
    }

    public function setAssociationName(?string $assocationName): self {
        $this->associationName = $assocationName;

        return $this;
    }

    public function getAssociationDescription(): ?string {
        return $this->associationDescription;
    }

    public function setAssociationDescription(?string $assocationDescription): self {
        $this->associationDescription = $assocationDescription;

        return $this;
    }

    public function getAmountFidelityPointToExchange(): ?int {
        return $this->amountFidelityPointToExchange;
    }

    public function setAmountFidelityPointToExchange(int $amountFidelityPointToExchange): self {
        $this->amountFidelityPointToExchange = $amountFidelityPointToExchange;

        return $this;
    }

    public function getAmountBalanceToAddAfterExchange(): ?string {
        return $this->amountBalanceToAddAfterExchange;
    }

    public function setAmountBalanceToAddAfterExchange(string $amountBalanceToAddAfterExchange): self {
        $this->amountBalanceToAddAfterExchange = $amountBalanceToAddAfterExchange;

        return $this;
    }

    public function isCotisantActivated(): ?bool {
        return $this->cotisantActivated;
    }

    public function setCotisantActivated(bool $cotisantActivated): self {
        $this->cotisantActivated = $cotisantActivated;

        return $this;
    }

    public function isPostActivated(): ?bool {
        return $this->postActivated;
    }

    public function setPostActivated(bool $postActivated): self {
        $this->postActivated = $postActivated;

        return $this;
    }
}
