<?php

namespace App\Manager;

use App\Entity\Client;
use Doctrine\ORM\EntityManagerInterface;

class ClientManager
{
      public $manager;

      public function __construct(EntityManagerInterface  $managerController)
      {
          $this->manager = $managerController;
      }

      public function isEmpty(?Client $client): bool
      {
            if ($client->getName() == "" && $client->getFirstname() == "" && $client->getLogin() == "" && $client->getPassword() == ""){
                  return true;
            }
            else{
                  return false;
            }
      }

      public function loginExists(?Client $client): bool
      {
          $clientRepository = $this->manager->getRepository(Client::class);

          $dupplicata = $clientRepository->findOneBy(["login" => $client->getLogin()]);

          return !is_null($dupplicata);
      }

      public function dataCorrect(?Client $client): bool
      {
          $regexSpecial = "#$%^&*()+=-[]';,./{}|:<>?~";
          $containsRegex = strpbrk($client->getPassword(), $regexSpecial);

          $nameUpperThree = (strlen($client->getName()) >=3);
          $firstNameUpperThree = (strlen($client->getFirstName()) >=3);
          $loginUpperFive = (strlen($client->getLogin()) >=5);
          $passwordUpperFive = (strlen($client->getPassword()) >=5);

          return ($containsRegex && $loginUpperFive && $passwordUpperFive & $firstNameUpperThree && $nameUpperThree);
      }

      public function persist(?Client $client)
      {
          $this->manager->persist($client);
          $this->manager->flush();
      }
}