<?php

namespace App\Entity;

use App\Repository\AssociationRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=AssociationRepository::class)
 */
class Association
{
    /**
     * @ORM\Id
     * @ORM\OneToOne(targetEntity = Client::class)
     * @ORM\JoinColumn(nullable = false, onDelete="CASCADE")
     */
    private Client $member;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private string $role;

    public function getMember(): Client
    {
        return $this->member;
    }

    public function setMember(Client $member): self
    {
        $this->member = $member;

        return $this;
    }

    public function getRole(): string
    {
        return $this->role;
    }

    public function setRole(string $role): self
    {
        $this->role = $role;

        return $this;
    }
}
