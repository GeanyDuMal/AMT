<?php

namespace App\Manager;

use App\Entity\Client;
use App\Entity\Member;
use App\Repository\MemberRepository;
use App\Utils\Enum\ClientType;
use App\Utils\Enum\MemberRole;
use App\Utils\Enum\SymfonyRole;
use Doctrine\ORM\EntityManagerInterface;

class MemberManager {
    private EntityManagerInterface $manager;
    private MemberRepository $memberRepository;

    public function __construct(EntityManagerInterface $entityManager) {
        $this->manager = $entityManager;
        $this->memberRepository = $this->manager->getRepository(Member::class);
    }

    public function persist(Member $member): void {
        $this->manager->persist($member);
        $this->manager->flush();
    }

    public function remove(Member $member): void {
        $this->manager->remove($member);
        $this->manager->flush();
    }

    /**
     * Verify in the table Association if there is already a president
     * If there is one or more (which isn't possible, but it prevents bug)
     * It removes every President
     * @param Member $associationMember
     * @return void
     */
    public function removeOtherPresidents(Member $associationMember): void {
        $members = $this->memberRepository->findBy(["role" => MemberRole::PRESIDENT]);

        foreach ($members as $otherMember) {
            if ($associationMember->getClient() !== $otherMember->getClient()) {
                //Here $otherMember is the President in Function
                $clientManager = new ClientManager($this->manager);
                $client = $otherMember->getClient();

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
     * @return Member
     */
    public function makeMember(Client $client, string $role): Member {
        $newMember = new Member();

        $client->setClientType(ClientType::ASSOCIATION);
        $newMember->setClient($client);
        $newMember->setRole($role);

        return $newMember;
    }

    public function getLowerOrEqualAssociationRole(Client $client): array {
        $association = $this->memberRepository->findOneBy(["client" => $client]);
        $roles = [];
        $associationRoles = MemberRole::getAll();

        // We use this way to filter because AssociationRole are ordered
        if ($client->getRoles()[0] != "ROLE_ADMIN") {
            foreach ($associationRoles as $role) {
                $roles[] = $role;
                if ($role == $association->getRole()) {
                    break;
                }
            }
        } else {
            $roles = $associationRoles;
        }

        return $roles;
    }
}