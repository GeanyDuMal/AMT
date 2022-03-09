<?php

namespace App\Manager;

use App\Entity\Association;
use App\Entity\AssociationRole;
use App\Entity\Client;
use App\Entity\ClientType;
use App\Repository\ClientTypeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectRepository;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class ClientManager
{
    public EntityManagerInterface $manager;
    public ObjectRepository $clientRepository;

    public function __construct(EntityManagerInterface $managerController)
    {
        $this->manager = $managerController;
        $this->clientRepository = $this->manager->getRepository(Client::class);
    }

    public function setData(Client $client, ClientTypeRepository $clientTypeRepository, UserPasswordHasherInterface $passwordHasher,
        String $name, String $firstName, String $login, ?String $password, String $balance, String $roleAssociationName, String $clientTypeName)
    {
        $client->setName($name)
            ->setFirstName($firstName)
            ->setLogin($login)
            ->setBalance($balance);

        $type = $clientTypeRepository->findOneBy(["name" => $clientTypeName]);
        $isStudent = (strcmp($clientTypeName,"Etudiant") == 0);
        if ($isStudent){
            $typeName = $clientTypeName;
        }else{
            $typeName = $roleAssociationName;
        }
        $role = $this->getRoleFromType($typeName);

        /*
            if password input exists so it's the add page
            so we have to set the password to the chosen one.
        */
        if($password){
            $hashedPassword=$passwordHasher->hashPassword($client, trim($password));
            $client->setPassword($hashedPassword);
        }

        $client->setRoles($role)
            ->setClientType($type);
    }

    /**
     * @param Client|null $client
     * @return void
     * Insert the Client in the Database
     */
    public function persist(?Client $client)
    {
        if (!$this->clientTableNotEmpty())
        {
            $clientTypeRepository = $this->manager->getRepository(ClientType::class);
            $client->setRoles(["ROLE_PRESIDENT"])
                   ->setClientType($clientTypeRepository->findOneBy(["name" => "Association"]));

            //Set the president of the association
            $associationRole = $this->manager->getRepository(AssociationRole::class)->findOneBy(["name" => "President"]);
            $association = new Association();
            $association->setMember($client)
                ->setRole($associationRole);

            $this->manager->persist($association);
            $this->manager->flush();
        }
        $this->checkIfRemoveFromAssociation($client);
        $this->verifyBalanceAndFidelity($client);
        $this->fidelityPointLimitCheck($client);

        $this->manager->persist($client);
        $this->manager->flush();
    }

    /**
     * @param Client $client
     * @return void
     * Remove the Client from the table Association if he is deleted
     */
    public function remove(Client $client){
        $clientTypeEtudiant = $this->manager->getRepository(ClientType::class)->findOneBy(["name" => "Etudiant"]);
        $client->setClientType($clientTypeEtudiant);

        $this->checkIfRemoveFromAssociation($client);

        $this->manager->remove($client);
        $this->manager->flush();
    }

    /**
     * @param Client|null $client
     * @return bool
     * Check if the differents attributes aren't empty
     * Don't check the attribute balance, fidelityPoint and clientType 
     */
    public function isNotFull(?Client $client): bool
    {
        if ($client->getName() == "" || $client->getFirstname() == "" || $client->getLogin() == "" || $client->getPassword() == ""){
            return true;
        }
        else {
            return false;
        }
    }

    /**
     * @param Client|null $client
     * @return bool
     * Check if the login is already assign to someone in the Database
     */
    public function loginExists(?Client $client): bool
    {

        $duplicata = $this->clientRepository->findOneBy(["login" => $client->getLogin()]);

        return !is_null($duplicata);
    }

    /**
     * @param Client|null $client
     * @return bool
     * Allow to verify the data :
     * Check if the password contains a special character
     * Check if the different input are the right lenght
     * Check if the name and first name doesn't contain a special character
     */
    public function dataCorrect(?Client $client): bool
    {
        if (!is_null($client)){
            $regexSpecial = "#$%^&*()+=-[]';,./{}|:<>?~";

            $containsSpecialPassword = $this->verifPassword($client->getPassword());
            $containsSpecialName = strpbrk($client->getName(), $regexSpecial);
            $containsSpecialFirstName = strpbrk($client->getFirstName(), $regexSpecial);

            $nameUpperTwo = (strlen($client->getName()) > 2);
            $firstNameUpperTwo = (strlen($client->getFirstName()) > 2);
            $loginUpperFour = (strlen($client->getLogin()) > 4);
            $passwordUpperFour = (strlen($client->getPassword()) > 4);

            return ($containsSpecialPassword && $loginUpperFour && $passwordUpperFour && $firstNameUpperTwo
                && $nameUpperTwo && !$containsSpecialName && !$containsSpecialFirstName);
        }
        else{
            return false;
        }
    }

    /**
     * @return boolean
     * Verify if the table Client isn't empty
     */
    public function clientTableNotEmpty(): bool
    {
        return sizeof($this->clientRepository->findAll()) > 0;
    }

    /**
     * @param String password
     * @return boolean
     * verify if the password contains regex and have the good size
     */
    public function verifPassword(String $password): bool
    {
        $regexSpecial = "#$%^&*()+=-[]';,./{}|:<>?~";

        return (strpbrk(trim($password), $regexSpecial) && strlen(trim($password)) >= 5);
    }

    public function getRoleFromType(string $typeName):array{
        $role=[];
        switch ($typeName){
            case "Tresorier":
                $role[] = "ROLE_TRESORIER";
                break;
            case "President":
                $role[] = "ROLE_PRESIDENT";
                break;
            case "Etudiant":
                $role[] = "ROLE_USER";
                break;
            default:
                $role[] = "ROLE_ASSOC";
                break;
        }
        return $role;
    }

    /**
     * @param Client $client
     * @return void
     */
    public function setRoleForClient(Client $client){
        $clientTypeRepository = $this->manager->getRepository(ClientType::class);

        $associationType = $this->$clientTypeRepository->findOneBy(["name" => "Association"]);

        /*
         * We check if the client is part of the association,
         * if it's the case we check it role,
         * else the client get the role user
         */
        switch ($client->getClientType()){
            case $associationType:
            {
                $associationRepository = $this->manager->getRepository(Association::class);

                switch ($associationRepository->findOneBy(["member" => $client])->getAssociationRole()->getName()){
                    case "President":
                        $client->setRoles(["ROLE_PRESIDENT"]);
                        break;
                    case "Tresorier":
                        $client->setRoles(["ROLE_TRESORIER"]);
                        break;
                    default:
                        $client->setRoles([ "ROLE_ASSOC"]);
                }
            }
            default:
                $client->setRoles(["ROLE_USER"]);
                break;
        }
    }

    public function getTypeFromRole(array $role):string{
        switch ($role[0]){
            case "ROLE_TRESORIER":
                $type = "Tresorier";
                break;
            case "ROLE_PRESIDENT":
                $type = "President";
                break;
            default:
                $type = "Membre";
                break;
        }
        return $type;
    }

    /**
     * return a Member made from the client in Parameter And a Role
     * @param Client $client
     * @param AssociationRole $role
     * @return Association
     */
    public function makeMember(Client $client, AssociationRole $role):Association
    {
        $newMember = new Association();

        $newMember->setMember($client);
        $newMember->setRole($role);

        return $newMember;
    }

    public function addFidelityPoint(float $amountOrder, Client $client): void{
          $client->setFidelityPoint($client->getFidelityPoint() + ($amountOrder * 10));
          $this->persist($client);
    }

    /**
     * @param Client $client
     * @return void
     * Remove the line in the table Association if the Client was in and doesn't have anymore the type "Association"
     */
    public function checkIfRemoveFromAssociation(Client $client){
        $clientTypeAssociation = $this->manager->getRepository(ClientType::class)->findOneBy(["name" => "Association"]);

        if ($client->getClientType() != $clientTypeAssociation){
            $associationMember = $this->manager->getRepository(Association::class)->findOneBy(["member" => $client]);
            if ($associationMember){
                $this->manager->remove($associationMember);
                $this->manager->flush();
            }
        }
    }

    /**
     * @param Client $client
     * @return void
     * Check if Balance and Fidelity Point are strictly positive
     */
    public function verifyBalanceAndFidelity(Client $client){
        if ($client->getBalance() == null || floatval($client->getBalance()) < 0){
            $client->setBalance(0);
        }
        if ($client->getFidelityPoint() == null || !is_numeric($client->getFidelityPoint()) || $client->getFidelityPoint() < 0 ){
            $client->setFidelityPoint(0);
        }
    }

    /**
     * @param Client $client
     * @return void
     * Verify if the limit of fidelity point is reached
     * If it's the case, it transform the fidelity point in an amount into the balance
     */
    public function fidelityPointLimitCheck(Client $client){
        $limitFidelityPoint = 150;
        $amountTransferToBalance = 0.8; // 1 = 1€

        if ($client->getFidelityPoint() >= $limitFidelityPoint){
            $client->setFidelityPoint($client->getFidelityPoint() - $limitFidelityPoint);
            $client->setBalance(floatval($client->getBalance()) + $amountTransferToBalance);
        }
    }
}