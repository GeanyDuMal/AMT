<?php

namespace App\Manager;

use App\Entity\Association;
use App\Repository\AssociationRepository;
use App\Repository\ClientRepository;
use App\Repository\ClientTypeRepository;
use Doctrine\ORM\EntityManagerInterface;

class AssociationManager
{
    public EntityManagerInterface $manager;
    public AssociationRepository $associationRepository;

    public function __construct(EntityManagerInterface $managerController)
    {
        $this->manager = $managerController;
        $this->associationRepository = (AssociationRepository::class)($this->manager->getRepository(Association::class));
    }

    /**
     * Verify in the table Association if there is already a president
     * If there is one or more (which isn't possible, but it prevents bug)
     * It removes every President
     */
    public function removeOtherPresidents(EntityManagerInterface $manager, Association $associationMember, ClientTypeRepository $clientTypeRepository, ClientRepository $clientRepository){
        $members = $this->associationRepository->findAll();

        foreach ($members as $otherMember){
            if($associationMember->getMember() !== $otherMember->getMember()){
                if($otherMember->getRole()->getName() == "President"){
                    $client = $clientRepository->findOneBy(['id'=> $otherMember->getMember()]);
                    $client->setClientType($clientTypeRepository->findOneBy(['name'=>'Etudiant']));
                    $client->setRoles(["ROLE_USER"]);
                    $manager->persist($client);
                    $manager->flush();
                }
            }
        }
    }
}