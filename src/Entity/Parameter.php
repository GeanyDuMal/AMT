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
    private ?string $linkLogo = null;

    /**
     * @ORM\Column(type="integer")
     */
    private ?int $amountFidelityPointToExchange = null;

    /**
     * @ORM\Column(type="integer")
     */
    private ?int $amountBalanceToAddAfterExchange = null;

    /**
     * @ORM\Column(type="boolean")
     */
    private ?bool $cotisantAllowed = null;

    public function getId(): ?int {
        return $this->id;
    }

    public function getLinkLogo(): ?string {
        return $this->linkLogo;
    }

    public function setLinkLogo(string $linkLogo): self {
        $this->linkLogo = $linkLogo;

        return $this;
    }

    public function getAmountFidelityPointToExchange(): ?int {
        return $this->amountFidelityPointToExchange;
    }

    public function setAmountFidelityPointToExchange(int $amountFidelityPointToExchange): self {
        $this->amountFidelityPointToExchange = $amountFidelityPointToExchange;

        return $this;
    }

    public function getAmountBalanceToAddAfterExchange(): ?int {
        return $this->amountBalanceToAddAfterExchange;
    }

    public function setAmountBalanceToAddAfterExchange(int $amountBalanceToAddAfterExchange): self {
        $this->amountBalanceToAddAfterExchange = $amountBalanceToAddAfterExchange;

        return $this;
    }

    public function isCotisantAllowed(): ?bool {
        return $this->cotisantAllowed;
    }

    public function setCotisantAllowed(bool $cotisantAllowed): self {
        $this->cotisantAllowed = $cotisantAllowed;

        return $this;
    }
}
