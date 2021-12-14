<?php

namespace App\Manager;

use App\Entity\Client;
use Doctrine\ORM\EntityManagerInterface;

class ClientManager
{
      public function isEmpty(?Client $client): bool
      {
            if ($client->getName() == "" && $client->getFirstname() == "" && $client->getLogin() == "" && $client->getPassword() == ""){
                  return true;
            }
            else{
                  return false;
            }
      }

      public function loginExists(EntityManagerInterface $manager, ?Client $client): bool
      {
          $clientRepository = $manager->getRepository(Client::class);

          $dupplicata = $clientRepository->findOneBy(["login" => $client->getLogin()]);

          return !is_null($dupplicata);
      }

      public function persist(EntityManagerInterface $manager, ?Client $client)
      {
          $manager->persist($client);
          $manager->flush();
      }
}