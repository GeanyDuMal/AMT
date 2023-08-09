<?php

namespace App\Controller\Client;

use App\Entity\Client;
use App\Manager\ClientManager;
use App\Manager\MemberManager;
use App\Manager\ParameterManager;
use App\Repository\ClientRepository;
use App\Repository\MemberRepository;
use App\Utils\Enum\ClientType;
use App\Utils\Enum\MemberRole;
use App\Utils\Enum\SymfonyRole;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class EditClientController extends AbstractController
{
    /**
     * @Route("/admin/client/edit/{!id}", name="editClient", methods={"GET", "POST"} )
     */
    public function index($id, UserPasswordHasherInterface $passwordHasher, Request $request, EntityManagerInterface $manager,
        MemberRepository $memberRepository, ClientRepository $clientRepository): Response {
        if (!$this->isGranted(SymfonyRole::SECRETAIRE)) {
            return $this->redirectToRoute('home');
        }

        $parameterManager = new ParameterManager($manager);
        $parameter = $parameterManager->getParameter();
        $data = $request->request;
        $client = $clientRepository->find($id);
        $user = $clientRepository->findOneBy(["login" => $this->getUser()->getUserIdentifier()]);
        $member = $memberRepository->findOneBy(["client" => $client]);
        $memberManager = new MemberManager($manager);
        $assosRoles = $memberManager->getLowerOrEqualAssociationRole($user);
        $message = "";
        $allowEdit = $this->isGranted($client->getRoles()[0]);

        if ($allowEdit && $data->count() > 0) {
            $clientManager = new ClientManager($manager);

            $clientManager->setData($client, $passwordHasher, $data->get("name"),
                                    $data->get("firstName"), $client->getLogin(), $data->get("password"),
                                    $data->get("balance"), $data->get("clientType"), $data->get("assosRoles"), $data->get("fidelityPoint"));


            // Verify the confirmity of a client and verify that the login correspond to the stored one
            if ($clientManager->verifyClient($client) && strcmp($client->getLogin(), $data->get('login'))) {
                /*
                 * If we set the ClientType Association, we need to put the client in the table Association
                 */
                if ($data->get("clientType") == ClientType::ASSOCIATION) {

                    /*
                     *  if admin changed the role of a member to another role
                     *  we have to change it too in association table
                     */
                    $this->manageMember($memberManager, $memberRepository, $client, $request->get("assosRoles"));
                }

                $clientManager->persist($client);
                if ($client->getClientType() == ClientType::ASSOCIATION) {
                    $member = $memberRepository->findOneBy(["client" => $client]);
                    if ($member->getRole() == MemberRole::PRESIDENT) {
                        $memberManager->removeOtherPresidents($member);
                    }
                }

                return $this->redirectToRoute('menuClient', [
                    "message" => "Modification effectué avec succès"
                ]);
            }
        } else if (!$allowEdit) {
            return $this->redirectToRoute('menuClient', [
                "message" => "Vous n'avez pas l'autorisation de modifier ce client",
            ]);
        }

        return $this->render('client/EditClient.html.twig', [
            "parameter" => $parameter,
            "assosRoles" => $assosRoles,
            "message" => $message,
            "client" => $client,
            "member" => $member,
            "allowEdit" => $allowEdit,
            "clientTypes" => ClientType::getAll()
        ]);
    }

    /**
     * @param MemberManager $memberManager
     * @param MemberRepository $memberRepository
     * @param Client $client
     * @param string $roleAssociation
     * @return void
     */
    private function manageMember(MemberManager $memberManager, MemberRepository $memberRepository, Client $client, string $roleAssociation): void {
        $member = $memberRepository->findOneBy(["client" => $client]);

        if ($member) {
            $member->setRole($roleAssociation);
        } else {
            $member = $memberManager->makeMember($client, $roleAssociation);
        }
        $memberManager->persist($member);
    }
}