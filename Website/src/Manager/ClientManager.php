<?php

namespace App\Manager;

use App\Entity\Client;
use App\Repository\ClientTypeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasher;
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
    public function getRoles(string $typeName):array{
        $role=array();
        switch ($typeName){
            case "Membre":$role[]="ROLE_USER";break;
            case "Trésorier":$role[] = "ROLE_TRESORIER";break;
            case "Président":$role[] = "ROLE_PRESIDENT";break;
            default:$role[] = "ROLE_ASSOC";break;
        }
        return $role;
    }
    public function setData(Client &$client, Request $request, ClientTypeRepository $clientTypeRepository,UserPasswordHasherInterface $passwordHasher){
        $data = $request->request;

        $client->setName(trim($data->get('name')));
        $client->setFirstName(trim($data->get('fname')));
        $client->setLogin(trim($data->get('login')));
        /*
            if password input exists so it's the add page
            so we have to initialize the fidelity points
            and set the password to the chosen one.
        */
        if($data->get('pwd')){
            $hashedPassword=$passwordHasher->hashPassword($client,trim($data->get('pwd')));
            $client->setPassword($hashedPassword);
            $client->setFidelityPoint(0);
        }
        $client->setBalance(trim($data->get('balance')));
        $typeAssos=$data->get('types');
        $typeClient=$data->get('clientType');
        $typeName= strcmp($typeClient,"Etudiant")==0?$typeClient:$typeAssos;
        $role= $this->getRoles($typeName);
        $client->setRoles($role);
        $type=$clientTypeRepository->findOneBy(["name"=>$typeName]);
        $client->setClientType($type);
    }

}