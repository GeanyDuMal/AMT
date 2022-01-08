<?php

namespace App\Manager;

use App\Entity\Association;
use App\Entity\Client;
use App\Repository\AssociationRoleRepository;
use App\Repository\ClientTypeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class ClientManager
{
    public EntityManagerInterface $manager;

    public function __construct(EntityManagerInterface $managerController)
    {
        $this->manager = $managerController;
    }

    /**
     * @param Client|null $client
     * @return void
     * Insert the Client in the Database
     */
    public function persist(?Client $client)
    {
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
        $clientRepository = $this->manager->getRepository(Client::class);

        $dupplicata = $clientRepository->findOneBy(["login" => $client->getLogin()]);

        return !is_null($dupplicata);
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

            $loginIsMail = $this->isMail($client->getLogin());
            $containsSpecialPassword = $this->verifPassword($client->getPassword());
            $containsSpecialName = strpbrk($client->getName(), $regexSpecial);
            $containsSpecialFirstName = strpbrk($client->getFirstName(), $regexSpecial);

            $nameUpperThree = (strlen($client->getName()) >=3);
            $firstNameUpperThree = (strlen($client->getFirstName()) >=3);
            $loginUpperSix = (strlen($client->getLogin()) >=6);
            $passwordUpperFive = (strlen($client->getPassword()) >=5);

            return ($containsSpecialPassword && $loginIsMail && $loginUpperSix && $passwordUpperFive && $firstNameUpperThree
                && $nameUpperThree && !$containsSpecialName && !$containsSpecialFirstName);
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
        $clientRepository = $this->manager->getRepository();
        // ToDo 
        return true;
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

    public function isMail(String $login): bool
    {
        //Fonction existante qui verifie si le string correspond bien à un mail
        return filter_var($login, FILTER_VALIDATE_EMAIL);
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

        public function addFidelityPoint(float $amountOrder, Client $client): void{
              $client->setFidelityPoint($client->getFidelityPoint() + ($amountOrder * 10));
              $this->persist($client);
        }
}