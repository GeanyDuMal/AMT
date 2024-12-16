<?php

namespace App\Entity;

use App\Repository\MemberRepository;
use App\Utils\Enum\MemberRoleEnum;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=MemberRepository::class)
 */
class Member
{
    /**
     * @ORM\Id
     * @ORM\OneToOne(targetEntity=Client::class)
     * @ORM\JoinColumn(nullable=false, onDelete="CASCADE")
     */
    private Client $client;

    /**
     * @ORM\Column(type="string", length=255, enumType=MemberRoleEnum::class)
     */
    private MemberRoleEnum $role;

    public function getClient(): Client
    {
        return $this->client;
    }

    public function setClient(Client $client): self
    {
        $this->client = $client;

        return $this;
    }

    public function getRole(): MemberRoleEnum
    {
        return $this->role;
    }

    public function setRole(MemberRoleEnum $role): self
    {
        $this->role = $role;

        return $this;
    }
}
