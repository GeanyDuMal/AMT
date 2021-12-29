<?php

namespace App\Entity;

use App\Repository\ClientRepository;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\ClientType;
use Exception;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;
/**
 * @ORM\Entity(repositoryClass=ClientRepository::class)
 */
class Client implements UserInterface, PasswordAuthenticatedUserInterface
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Length(
     *      min = 3,
     *      minMessage = "Votre nom doit comporter au moins {{ limit }} caractères",
     * )
     */
    private $name;
    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Length(
     *      min = 3,
     *      minMessage = "Votre prénom doit comporter au moins {{ limit }} caractères",
     *)
     */
    private $firstName;
    /**
     * @ORM\Column(type="string", length=255, unique=true)
     * @Assert\Length(
     *      min = 5,
     *      minMessage = "Votre login doit comporter au moins {{ limit }} caractères",
     *     )
     */
    private $login;
    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank()
     */
    /*
     *     * @Assert\All
     * ({
     * @Assert\Length(
     *      min = 5,
     *      minMessage = "Votre mot de passe doit comporter au moins {{ limit }} caractères",
     *     ),
     * @Assert\Regex(
     *     pattern="/[^a-zA-Z\d]/",
     *     message="le mot de passe doit contenir au moins un caractére spéciale",
     * )
     * })
     * */
    private $password;
    /**
     * @ORM\Column(type="decimal", precision=5, scale=2, nullable=true ,options={"default": 0})
     */
    /*
     *      * /@Assert\All({
     *      @Assert\NotBlank,
     *      @Assert\PositiveOrZero(
     *       message="Le balance est positive"
     *      )
     * })
     * */
    private $balance;
    /**
     * @ORM\Column(type="integer", nullable=true  ,options={"default": 0})
     * @Assert\PositiveOrZero(
     *     message="Les points de fidilité sont positive"
     * )
     */
    private $fidelityPoint;

    /**
     * @ORM\ManyToOne(targetEntity=ClientType::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private $clientType;

    /**
     * @ORM\Column(type="json",options={"default" = "ROLE_USER"})
     */
    private $roles = [];

    public function getId(): ?int
    {
        return $this->id;
    }
    public function setID(int $id): self
    {
        $this->id = $id;
        return $this;
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

    public function getRoles(): array
    {
        $roles = $this->roles;

        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * Security Part
     * Methods implemented by the UserInterface
     */

    public function getSalt()
    {
        // you *may* need a real salt depending on your encoder
        // see section on salt below
        return null;
    }

    public function getUserIdentifier(): ?string
    {
        return $this->getLogin();
    }

    public function eraseCredentials()
    {
        // TODO: Implement eraseCredentials() method.
    }

    public function getUsername(): ?string
    {
        return $this->getUserIdentifier();
    }
}
