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
    public $manager;

    public function __construct(EntityManagerInterface $managerController)
    {
        $this->manager = $managerController;
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
            else{
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

              $containsSpecialPassword = $this->verifPassword($client->getPassword());
              $containsSpecialName = strpbrk($client->getName(), $regexSpecial);
              $containsSpecialFirstName = strpbrk($client->getFirstName(), $regexSpecial);

              $nameUpperThree = (strlen($client->getName()) >=3);
              $firstNameUpperThree = (strlen($client->getFirstName()) >=3);
              $loginUpperFive = (strlen($client->getLogin()) >=5);
              $passwordUpperFive = (strlen($client->getPassword()) >=5);

              return ($containsSpecialPassword && $loginUpperFive && $passwordUpperFive && $firstNameUpperThree
                  && $nameUpperThree && !$containsSpecialName && !$containsSpecialFirstName);
          }
          else{
              return false;
          }
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
     * @param String password
     * @return boolean
     * verify if the password contains regex
     */
      public function verifPassword(String $password): bool
      {
          $regexSpecial = "#$%^&*()+=-[]';,./{}|:<>?~";

          return strpbrk($password, $regexSpecial);
      }

    public function addFidelityPoint(int $amountOrder, Client $client): void{
          $client->setFidelityPoint($client->getFidelityPoint() + ($amountOrder * 10));
    }
}