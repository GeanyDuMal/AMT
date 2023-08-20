<?php

namespace App\Manager;

use App\Entity\Client;
use App\Entity\Member;
use App\Entity\PasswordForgotRequest;
use App\Repository\ClientRepository;
use App\Utils\Enum\ClientType;
use App\Utils\Enum\MemberRole;
use App\Utils\Enum\SymfonyRole;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;


class ClientManager {
    private EntityManagerInterface $manager;
    private ClientRepository $clientRepository;
    const REGEX_SPECIAL = "@#$%^&*()+=-[]';,./{}|:<>?~";

    public function __construct(EntityManagerInterface $entityManager) {
        $this->manager = $entityManager;
        $this->clientRepository = $this->manager->getRepository(Client::class);
    }

    public function persist(Client $client): void {
        $this->defineCreationDateIfNecessary($client);

        $this->setPresidentIfNecessary($client);

        $this->removeFromAssociationIfNecessary($client);
        $this->correctBalanceAndFidelity($client);
        $this->fidelityPointLimitCheck($client);

        $this->manager->persist($client);
        $this->manager->flush();
    }

    public function remove(Client $client): void {
        /**
         * @TODO Ne pas check les roles Symfony (sauf pour ADMIN)
         */
        if (!in_array([SymfonyRole::PRESIDENT, "ROLE_ADMIN"], $client->getRoles())) {
            $client->setClientType(ClientType::ETUDIANT);

            $this->removeFromAssociationIfNecessary($client);
            $this->deletePasswordForgotRequest($client);

            $this->manager->remove($client);
            $this->manager->flush();
        }
    }

    /**
     * Remove the line in the table Association if the Client was in and doesn't have the type "Association" anymore
     * @param Client $client
     * @return void
     */
    public function removeFromAssociationIfNecessary(Client $client): void {
        if ($client->getClientType() != ClientType::ASSOCIATION) {
            $member = $this->manager->getRepository(Member::class)->findOneBy(["client" => $client]);
            // If client is present in table Association, it's not normal, so we remove it
            if ($member) {
                $memberManager = new MemberManager($this->manager);

                $member->getClient()->setClientType($client->getClientType());

                $memberManager->remove($member);
            }
        }
    }

    /**
     * Create a client with verifying the data assigned
     * @param Client $client
     * @param UserPasswordHasherInterface $passwordHasher
     * @param string $name
     * @param string $firstName
     * @param string $login
     * @param string|null $password
     * @param string $balance
     * @param string|null $roleAssociationName
     * @param string $clientType
     * @param int|null $fidelityPoint
     * @return void
     */
    public function setData(Client $client, UserPasswordHasherInterface $passwordHasher, string $name,
        string $firstName, string $login, ?string $password, string $balance, string $clientType,
        ?string $roleAssociationName, ?int $fidelityPoint = 0): void {

        $client->setName(strtoupper($name))
            ->setFirstName($firstName)
            ->setLogin($login)
            ->setBalance($balance)
            ->setFidelityPoint($fidelityPoint)
            ->setClientType($clientType)
            ->setRoles([SymfonyRole::USER]);

        $this->setRoleForClient($client, $roleAssociationName);
        $this->fidelityPointLimitCheck($client);

        /*
            if password input exists, so it's the add page,
            so we have to set the password to the chosen one.
        */
        if ($password) {
            $hashedPassword = $passwordHasher->hashPassword($client, trim($password));
            $client->setPassword($hashedPassword);
        }
    }

    /**
     * Check if the differents attributes aren't empty
     * Don't check the attribute balance, fidelityPoint and clientType
     * @param Client $client
     * @return bool
     */
    public function isNotFull(Client $client): bool {
        return ($client->getName() == "" || $client->getFirstname() == "" || $client->getLogin() == "" ||
            $client->getPassword() == "" || $client->getBalance() == "" || $client->getClientType() == "" ||
            $client->getFidelityPoint() == "");
    }

    /**
     * Check if the client already exist in the Database
     * @param Client $client
     * @return bool
     */
    public function clientExists(Client $client): bool {
        $duplicataLogin = $this->clientRepository->findOneBy(["login" => $client->getLogin()]);
        $duplicataNameFirstName = $this->clientRepository->findOneBy([
            "name" => $client->getName(),
            "firstName" => $client->getFirstName()
        ]);

        return !is_null($duplicataLogin) || !is_null($duplicataNameFirstName);
    }

    /**
     * Verify the data :
     * Check if the different input are the right lenght
     * Check if the name and first name doesn't contain a special character
     * @param Client $client
     * @return bool
     */
    public function verifyClient(Client $client): bool {
        $containsSpecialName = strpbrk($client->getName(), self::REGEX_SPECIAL);
        $containsSpecialFirstName = strpbrk($client->getFirstName(), self::REGEX_SPECIAL);

        $nameUpperTwo = (strlen($client->getName()) > 2);
        $firstNameUpperTwo = (strlen($client->getFirstName()) > 2);
        $loginUpperFour = (strlen($client->getLogin()) > 4);
        $passwordUpperFour = (strlen($client->getPassword()) > 4);

        $this->correctBalanceAndFidelity($client);

        return ($loginUpperFour && $passwordUpperFour && $firstNameUpperTwo
            && $nameUpperTwo && !$containsSpecialName && !$containsSpecialFirstName &&
            in_array($client->getClientType(), ClientType::getAll()));
    }

    /**
     * Verify if the password contains regex and have the good size
     * @param String $password password not hashed
     * @return boolean
     */
    public function verifyPassword(string $password): bool {
        return (strpbrk(trim($password), self::REGEX_SPECIAL) && strlen(trim($password)) >= 5);
    }

    /**
     * Define the @SymfonyRole Corresponding to the Client
     * @param Client $client
     * @param string|null $roleAssociation the role of the client in the association, not null if $client->clientType is Association
     * @return void
     */
    public function setRoleForClient(Client $client, ?string $roleAssociation): void {
        /*
         * If the clientType is Association, $roleAssociation is not null
         */
        switch ($client->getClientType()) {
            case ClientType::ASSOCIATION:
            {
                switch ($roleAssociation) {
                    case MemberRole::PRESIDENT:
                        $client->setRoles([SymfonyRole::PRESIDENT]);
                        break;
                    case (MemberRole::TRESORIER || MemberRole::VICE_PRESIDENT):
                        $client->setRoles([SymfonyRole::TRESORIER]);
                        break;
                    case MemberRole::SECRETAIRE:
                        $client->setRoles([SymfonyRole::SECRETAIRE]);
                        break;
                    default:
                        $client->setRoles([SymfonyRole::ASSOC]);
                        break;
                }
                break;
            }
            default:
                $client->setRoles([SymfonyRole::USER]);
                break;
        }
    }

    /**
     * Add the fidelityPoint to a client after an ordered
     * @param float $amountOrder
     * @param Client $client
     * @return void
     */
    public function addFidelityPoint(float $amountOrder, Client $client): void {
        $client->setFidelityPoint($client->getFidelityPoint() + ($amountOrder * 10));
        $this->persist($client);
    }

    /**
     * Check if Balance and Fidelity Point are strictly positive
     * @param Client $client
     * @return void
     */
    public function correctBalanceAndFidelity(Client $client): void {
        if ($client->getBalance() == null || floatval($client->getBalance()) < 0) {
            $client->setBalance(0);
        }
        if (!is_numeric($client->getFidelityPoint()) || $client->getFidelityPoint() < 0) {
            $client->setFidelityPoint(0);
        }
    }

    /**
     * Verify if the limit of fidelity point is reached
     * If it's the case, it transforms the fidelity point in an amount into the balance
     * @param Client $client
     * @return void
     */
    public function fidelityPointLimitCheck(Client $client): void {
        $parameterManager = new ParameterManager(($this->manager));
        $parameter = $parameterManager->getParameter();

        $limitFidelityPoint = $parameter->getAmountFidelityPointToExchange();
        $amountTransferToBalance = floatval($parameter->getAmountBalanceToAddAfterExchange());
        $nbReduction = intdiv($client->getFidelityPoint(), $limitFidelityPoint);

        if ($nbReduction != 0) {
            $client->setFidelityPoint($client->getFidelityPoint() - $nbReduction * $limitFidelityPoint);
            $client->setBalance(floatval($client->getBalance()) + $nbReduction * $amountTransferToBalance);
        }
    }

    /**
     * If there is no President in the database, it will assign the current Client as a President
     * @param Client $client
     * @return void
     */
    private function setPresidentIfNecessary(Client $client): void {
        if (!(sizeof($this->clientRepository->findAll()) > 0)) {
            $client->setRoles([SymfonyRole::PRESIDENT])
                ->setClientType(ClientType::ASSOCIATION);

            //Set the president of the association
            $member = new Member();
            $member->setClient($client)
                ->setRole(MemberRole::PRESIDENT);

            $this->manager->persist($member);
            $this->manager->flush();
        }
    }

    /**
     * Delete the PasswordForgotRequest for the $client if the request exist
     * @param Client $client
     * @return void
     */
    public function deletePasswordForgotRequest(Client $client): void {
        $passwordForgotRequestManager = new PasswordForgotRequestManager($this->manager);

        $passwordForgotRequest = $this->manager->getRepository(PasswordForgotRequest::class)->findOneBy(["client" => $client]);

        if ($passwordForgotRequest) {
            $passwordForgotRequestManager->remove($passwordForgotRequest);
        }
    }

    /**
     * If the $client doesn't exist in database, we set him the creationDate attribute to now
     * @param Client $client
     * @return void
     */
    private function defineCreationDateIfNecessary(Client $client): void {
        if (!$this->clientExists($client)) {
            $client->setCreationDate(new DateTime("now"));
        }
    }

    /**
     * @return array ClientTypes activated in parameter
     */
    public function getClientTypes(): array {
        $parameterManager = new ParameterManager(($this->manager));
        $parameter = $parameterManager->getParameter();

        $clientTypes = ClientType::getAll();

        if (!$parameter->isCotisantActivated()) {
            foreach ($clientTypes as $clientType) {
                if ($clientType == ClientType::COTISANT) {
                    unset($clientTypes[array_search($clientType, $clientTypes, true)]);
                }
            }
        }

        return $clientTypes;
    }

    public function getClientById(string $id): ?Client {
        return $this->clientRepository->find($id);
    }
}