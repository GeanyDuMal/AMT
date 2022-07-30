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
        $this->changeMemberTypeAdd($association);

        $this->manager->persist($association);
        $this->manager->flush();
    }

    public function remove(Association $association): void
    {
        $this->changeMemberTypeRemove($association);

        $this->manager->remove($association);
        $this->manager->flush();
    }

    /**
     * @param Association $association
     * @return void
     */
    public function changeMemberTypeAdd(Association $association): void
    {
        $client = $association->getMember();
        $client->setClientType(ClientType::ASSOCIATION);

        $this->manager->persist($client);
        $this->manager->flush();
    }

    /**
     * @param Association $association
     * @return void
     */
    public function changeMemberTypeRemove(Association $association): void
    {
        $client = $association->getMember();
        $client->setClientType(ClientType::ETUDIANT);

        $this->manager->persist($client);
        $this->manager->flush();
    }

    /**
     * Verify in the table Association if there is already a president
     * If there is one or more (which isn't possible, but it prevents bug)
     * It removes every President
     */
    /**
     * @param Association $associationMember
     * @return void
     * Remove every presisent
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
     * return a Member made from the Client in parameter and a Role
     * @param Client $client
     * @param string $role
     * @return Association
     * Create an Association entity with a client and a role
     */
    public function makeMember(Client $client, string $role): Association
    {
        $newMember = new Association();

        $newMember->setMember($client);
        $newMember->setRole($role);

        return $newMember;
    }
}