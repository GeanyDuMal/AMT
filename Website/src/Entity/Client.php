<?php

namespace App\Entity;

use App\Repository\ClientRepository;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\ClientType;
use Exception;
use JetBrains\PhpStorm\Internal\LanguageLevelTypeAware;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Entity(repositoryClass=ClientRepository::class)
 * @method string getUserIdentifier()
 */
class Client implements UserInterface, \Serializable
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
    private $name;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $firstName;

    /**
     * @ORM\Column(type="string", length=255, unique=true)
     */
    private $login;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $password;

    /**
     * @ORM\Column(type="decimal", precision=5, scale=2, nullable=true ,options={"default": 0})
     */
    private $balance;

    /**
     * @ORM\Column(type="integer", nullable=true  ,options={"default": 0})
     */
    private $fidelityPoint;

    /**
     * @ORM\ManyToOne(targetEntity=ClientType::class)
     * @ORM\JoinColumn(nullable=true)
     */
    private $clientType;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): self
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getLogin(): ?string
    {
        return $this->login;
    }

    public function setLogin(string $login): self
    {
        $this->login = $login;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getBalance(): ?string
    {
        return $this->balance;
    }

    public function setBalance(string $balance): self
    {
        $this->balance = $balance;

        return $this;
    }

    public function getFidelityPoint(): ?int
    {
        return $this->fidelityPoint;
    }

    public function setFidelityPoint(int $fidelityPoint): self
    {
        $this->fidelityPoint = $fidelityPoint;

        return $this;
    }

    public function getClientType(): ?ClientType
    {
        return $this->clientType;
    }

    public function setClientType(?ClientType $clientType): self
    {
        $this->clientType = $clientType;

        return $this;
    }

    /**
     * Security Part
     * Methods implemented by the UserInterface
     */

    public function getRoles()
    {

        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function getSalt()
    {
        // you *may* need a real salt depending on your encoder
        // see section on salt below
        return null;
    }

    public function eraseCredentials()
    {
        // TODO: Implement eraseCredentials() method.
    }

    public function getUsername()
    {
        return $this->getLogin();
    }

    public function __call($name, $arguments)
    {
        // TODO: Implement @method string getUserIdentifier()
    }

    /**
     * Serialize Part
     * Implemented by the Serialize Interface
     */

    public function serialize()
    {
        return serialize(array(
            $this->id,
            $this->login,
            $this->password,
            // see section on salt below
            // $this->salt,
        ));
    }

    public function unserialize($serialized)
    {
        list (
            $this->id,
            $this->login,
            $this->password,
            // see section on salt below
            // $this->salt
            ) = unserialize($serialized, array('allowed_classes' => false));
    }
}
