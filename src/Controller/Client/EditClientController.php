<?php

namespace App\Controller\Client;

use App\Entity\Client;
use App\Manager\ClientManager;
use App\Manager\MemberManager;
use App\Manager\ParameterManager;
use App\Repository\ClientRepository;
use App\Repository\MemberRepository;
use App\Utils\Enum\ClientTypeEnum;
use App\Utils\Enum\MemberRoleEnum;
use App\Utils\Enum\SymfonyRoleEnum;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class EditClientController extends AbstractController {

    private EntityManagerInterface $manager;
    private ClientManager $clientManager;
    private MemberManager $memberManager;
    private ParameterManager $parameterManager;
    private ClientRepository $clientRepository;
    private MemberRepository $memberRepository;
    private UserPasswordHasherInterface $userPasswordHasher;

    public function __construct(UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $manager, MemberRepository $memberRepository,
        ClientRepository $clientRepository) {
        $this->manager = $manager;
        $this->parameterManager = new ParameterManager($this->manager);
        $this->clientManager = new ClientManager($this->manager);
        $this->memberManager = new MemberManager($this->manager);
        $this->memberRepository = $memberRepository;
        $this->clientRepository = $clientRepository;
        $this->userPasswordHasher = $userPasswordHasher;
    }

    #[Route("/admin/client/edit/{!id}", name: "editClient", methods: ["GET", "POST"])]
    public function index($id, Request $request): Response {
        if (!$this->isGranted(SymfonyRoleEnum::SECRETAIRE->value)) {
            return $this->redirectToRoute('home');
        }

        $parameter = $this->parameterManager->getParameter(true);
        $data = $request->request;
        $client = $this->clientRepository->find($id);
        $user = $this->clientRepository->findOneBy(["login" => $this->getUser()->getUserIdentifier()]);
        $member = $this->memberRepository->findOneBy(["client" => $client]);
        $assosRoles = $this->memberManager->getLowerOrEqualAssociationRole($user);
        $message = "";
        $allowEdit = $this->isGranted($client->getRoles()[0]);

        if ($allowEdit && $data->count() > 0) {
            $this->clientManager->setData($client,
                                          $this->userPasswordHasher,
                                          $data->get("name"),
                                          $data->get("firstName"),
                                          $client->getLogin(),
                                          $data->get("password"),
                                          $data->get("balance"),
                                          ClientTypeEnum::from($data->get("clientType")),
                                          $data->get("assosRoles"),
                                          $data->get("fidelityPoint"));


            // Verify the confirmity of a client and verify that the login correspond to the stored one
            if ($this->clientManager->verifyClient($client) && strcmp($client->getLogin(), $data->get('login'))) {
                /*
                 * If we set the ClientTypeEnum Association, we need to put the client in the table Association
                 */
                if ($data->get("clientType") == ClientTypeEnum::ASSOCIATION->value) {

                    /*
                     *  if admin changed the role of a member to another role
                     *  we have to change it too in association table
                     */
                    $this->manageMember($client, MemberRoleEnum::from($request->get("assosRoles")));
                }

                $this->clientManager->persist($client);
                if ($client->getClientType() == ClientTypeEnum::ASSOCIATION) {
                    $member = $this->memberRepository->findOneBy(["client" => $client]);
                    if ($member && $member->getRole() == MemberRoleEnum::PRESIDENT) {
                        $this->memberManager->removeOtherPresidents($member);
                    }
                }

                return $this->redirectToRoute('menuClient', [
                    "message" => "Modification effectué avec succès"
                ]);
            }
        } else {
            if (!$allowEdit) {
                return $this->redirectToRoute('menuClient', [
                    "message" => "Vous n'avez pas l'autorisation de modifier ce client",
                ]);
            }
        }

        return $this->render('client/EditClient.html.twig', [
            "parameter" => $parameter,
            "assosRoles" => $assosRoles,
            "message" => $message,
            "client" => $client,
            "member" => $member,
            "allowEdit" => $allowEdit,
            "clientTypes" => $this->clientManager->getLowerOrEqualClientTypes($user)
        ]);
    }

    /**
     * @param Client $client
     * @param MemberRoleEnum $roleAssociation
     * @return void
     */
    private function manageMember(Client $client, MemberRoleEnum $roleAssociation): void {
        $member = $this->memberRepository->findOneBy(["client" => $client]);

        if ($member) {
            $member->setRole($roleAssociation);
        } else {
            $member = $this->memberManager->makeMember($client, $roleAssociation);
        }
        $this->memberManager->persist($member);
    }
}