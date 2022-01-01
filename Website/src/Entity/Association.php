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
     * @ORM\OneToOne(targetEntity=Client::class, cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=false,onDelete="CASCADE")
     */
    private $member;

    /**
     * @ORM\ManyToOne(targetEntity=AssociationRole::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private $role;

    public function getMember(): ?Client
    {
        return $this->member;
    }

    public function setMember(Client $member): self
    {
        $this->member = $member;

        return $this;
    }

    public function getRole(): ?AssociationRole
    {
        return $this->role;
    }

    public function setRole(?AssociationRole $role): self
    {
        $this->role = $role;

        return $this;
    }
}
