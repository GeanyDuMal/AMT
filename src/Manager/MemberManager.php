<?php

namespace App\Manager;

use App\Entity\Client;
use App\Entity\Member;
use App\Repository\MemberRepository;
use App\Utils\Enum\ClientTypeEnum;
use App\Utils\Enum\MemberRoleEnum;
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
        $members = $this->memberRepository->findBy(["role" => MemberRoleEnum::PRESIDENT]);

        foreach ($members as $otherMember) {
            if ($associationMember->getClient() !== $otherMember->getClient()) {
                //Here $otherMember is the President in Function
                $clientManager = new ClientManager($this->manager);
                $client = $otherMember->getClient();

                $client->setClientType(ClientTypeEnum::ETUDIANT);
                $client->setRoles([SymfonyRole::USER]);

                $this->remove($otherMember);
                $clientManager->persist($client);
            }
        }
    }

    /**
     * Return a Member made from the Client in parameter and a Role
     * @param Client $client
     * @param MemberRoleEnum $role
     * @return Member
     */
    public function makeMember(Client $client, MemberRoleEnum $role): Member {
        $newMember = new Member();

        $client->setClientType(ClientTypeEnum::ASSOCIATION);
        $newMember->setClient($client);
        $newMember->setRole($role);

        return $newMember;
    }

    public function getLowerOrEqualAssociationRole(Client $client): array {
        $member = $this->memberRepository->findOneBy(["client" => $client]);
        $rolesReturned = [];
        $memberRoles = MemberRoleEnum::cases();

        if ($member) {
            foreach ($memberRoles as $role) {
                $rolesReturned[] = $role;
                if ($role == $member->getRole()) {
                    break;
                }
            }
        } else if ($client->getRoles()[0] == SymfonyRole::ADMIN) {
            $rolesReturned = $memberRoles;
        }

        return $rolesReturned;
    }
}