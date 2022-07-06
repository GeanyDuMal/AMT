<?php

namespace App\Manager;

use App\Entity\Association;
use App\Repository\ClientRepository;
use App\Utils\Enum\AssociationRole;
use App\Utils\Enum\ClientType;
use App\Utils\Enum\SymfonyRole;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectRepository;

class AssociationManager
{
    public EntityManagerInterface $manager;
    public ObjectRepository $associationRepository;

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

    public function changeMemberTypeAdd(Association $association): void
    {
        $client = $association->getMember();
        $client->setClientType(ClientType::ASSOCIATION);

        $this->manager->persist($client);
        $this->manager->flush();
    }

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
    public function removeOtherPresidents(EntityManagerInterface $manager, Association $associationMember,
                                          ClientRepository $clientRepository): void
    {
        $members = $this->associationRepository->findAll();

        foreach ($members as $otherMember) {
            if ($associationMember->getMember() !== $otherMember->getMember()) {
                if ($otherMember->getRole() == AssociationRole::PRESIDENT) {
                    //Here $otherMember is the President in Function

                    $client = $clientRepository->findOneBy(['id' => $otherMember->getMember()]);

                    $client->setClientType(ClientType::ETUDIANT);
                    $client->setRoles([SymfonyRole::USER]);

                    $manager->remove($otherMember);
                    $manager->persist($client);
                    $manager->flush();
                }
            }
        }
    }
}