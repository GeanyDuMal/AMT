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
        String $name, String $firstName, String $login, ?String $password, String $balance, String $roleAssociationName, String $clientTypeName
        ){
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
        if (!$this->checkMoreOneClient())
        {
            $clientTypeRepository = $this->manager->getRepository(ClientType::class);
            $client->setRoles(["ROLE_PRESIDENT"])
                   ->setClientType($clientTypeRepository->findOneBy(["name" => "Association"]));
        }
        $this->manager->persist($client);
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
     * verify if there is more than one client in the database
     */
    public function checkMoreOneClient(): bool
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

        return (strpbrk($password, $regexSpecial) && strlen($password) >= 5);
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
     * return a Member made from the client in Parameter
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
}