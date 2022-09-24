<?php

namespace App\Manager;

use App\Entity\Association;
use App\Entity\Client;
use App\Repository\AssociationRepository;
use App\Repository\ClientRepository;
use App\Utils\Enum\AssociationRole;
use App\Utils\Enum\ClientType;
use App\Utils\Enum\SymfonyRole;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectRepository;

class AssociationManager
{
    public EntityManagerInterface $manager;
    public AssociationRepository $associationRepository;

    public function __construct(EntityManagerInterface $managerController)
    {
        $this->manager = $managerController;
        $this->associationRepository = $this->manager->getRepository(Association::class);
    }

    public function persist(Association $association): void
    {
        $this->changeClientType($association, ClientType::ASSOCIATION);

        $this->manager->persist($association);
        $this->manager->flush();
    }

    public function remove(Association $association): void
    {
        $this->changeClientType($association, ClientType::ETUDIANT);

        $this->manager->remove($association);
        $this->manager->flush();
    }

    /**
     * Modify the client type of Association->member
     * @param Association $association
     * @param string $clientTypeToSet
     */
    public function changeClientType(Association $association, string $clientTypeToSet): void
    {
        $client = $association->getMember();
        if ($client->getClientType() != $clientTypeToSet){
            $client->setClientType($clientTypeToSet);

            $clientManager = new ClientManager($this->manager);
            $clientManager->persist($client);
        }
    }

    /**
     * Verify in the table Association if there is already a president
     * If there is one or more (which isn't possible, but it prevents bug)
     * It removes every President
     * @param Association $associationMember
     * @return void
     */
    public function removeOtherPresidents(Association $associationMember): void
    {
        $members = $this->associationRepository->find(["role" => AssociationRole::PRESIDENT]);

        foreach ($members as $otherMember) {
            if ($associationMember->getMember() !== $otherMember->getMember()) {
                //Here $otherMember is the President in Function
                $clientManager = new ClientManager($this->manager);
                $client = $otherMember->getMember();

                $client->setClientType(ClientType::ETUDIANT);
                $client->setRoles([SymfonyRole::USER]);

                $this->remove($otherMember);
                $clientManager->persist($client);
            }
        }
    }


    /**
     * Return a Member made from the Client in parameter and a Role
     * @param Client $client
     * @param string $role
     * @return Association
     */
    public function makeMember(Client $client, string $role): Association
    {
        $newMember = new Association();

        $newMember->setMember($client);
        $newMember->setRole($role);

        return $newMember;
    }
}