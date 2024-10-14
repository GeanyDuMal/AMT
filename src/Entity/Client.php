<?php

namespace App\Entity;

use App\Repository\ClientRepository;
use App\Utils\Enum\ClientTypeEnum;
use App\Utils\Enum\SymfonyRoleEnum;
use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=ClientRepository::class)
 */
class Client implements UserInterface, PasswordAuthenticatedUserInterface {
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private int $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Length(
     *      min = 3,
     *      minMessage = "Votre nom doit comporter au moins {{ limit }} caractères",
     *      )
     */
    private string $name;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Length(
     *      min = 3,
     *      minMessage = "Votre prénom doit comporter au moins {{ limit }} caractères",
     *      )
     */
    private string $firstName;

    /**
     * @ORM\Column(type="string", length=255, unique=true)
     * @Assert\Length(
     *      min = 5,
     *      minMessage = "Votre login doit comporter au moins {{ limit }} caractères",
     *     )
     */
    private string $login;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Length(
     *      min = 5,
     *      minMessage = "Votre password doit comporter au moins {{ limit }} caractères",
     *     )
     */
    private string $password;

    /**
     * @ORM\Column(type="decimal", precision=5, scale=2, options={"default": "0.00"})
     * @Assert\PositiveOrZero(
     *      message="La balance doit etre positive"
     *      )
     */
    private string $balance;

    /**
     * @ORM\Column(type="integer", options={"default": 0})
     * @Assert\PositiveOrZero(
     *      message="Les points de fidelité doivent etre positif"
     *      )
     */
    private int $fidelityPoint;

    /**
     * @ORM\Column(type="string", enumType=ClientTypeEnum::class)
     */
    private ClientTypeEnum $clientType;

    /**
     * @ORM\Column(type="json")
     */
    private array $roles = [];

    /**
     * @ORM\Column(type="date")
     */
    private DateTimeInterface $creationDate;

    public function getId(): int {
        return $this->id;
    }

    public function setId(int $id): self {
        $this->id = $id;
        return $this;
    }

    public function getName(): string {
        return $this->name;
    }

    public function setName(string $name): self {
        $this->name = $name;

        return $this;
    }

    public function getFirstName(): string {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): self {
        $this->firstName = $firstName;

        return $this;
    }

    public function getLogin(): string {
        return $this->login;
    }

    public function setLogin(string $login): self {
        $this->login = $login;
        return $this;
    }

    public function getPassword(): string {
        return $this->password;
    }

    public function setPassword(string $password): self {
        $this->password = $password;

        return $this;
    }

    public function getBalance(): string {
        return $this->balance;
    }

    public function setBalance(string $balance): self {
        $this->balance = $balance;

        return $this;
    }

    public function getFidelityPoint(): int {
        return $this->fidelityPoint;
    }

    public function setFidelityPoint(int $fidelityPoint): self {
        $this->fidelityPoint = $fidelityPoint;

        return $this;
    }

    public function getClientType(): ClientTypeEnum {
        return $this->clientType;
    }

    public function setClientType(ClientTypeEnum $clientType): self {
        $this->clientType = $clientType;

        return $this;
    }

    public function getCreationDate(): ?\DateTimeInterface {
        return $this->creationDate;
    }

    public function setCreationDate(\DateTimeInterface $creationDate): self {
        $this->creationDate = $creationDate;

        return $this;
    }

    public function getRoles(): array {
        $roles = $this->roles;

        ($roles == [] ? $roles = [SymfonyRoleEnum::USER] : true);

        return array_unique($roles);
    }

    public function setRoles(array $roles): self {
        $this->roles = $roles;

        return $this;
    }

    /**
     * Security Part
     * Methods implemented by the UserInterface
     */

    public function getSalt(): ?string {
        // you *may* need a real salt depending on your encoder
        // see section on salt below
        return null;
    }

    public function getUserIdentifier(): string {
        return $this->getLogin();
    }

    public function eraseCredentials() {
        // TODO: Implement eraseCredentials() method.
    }

    public function getUsername(): string {
        return $this->getUserIdentifier();
    }
}
