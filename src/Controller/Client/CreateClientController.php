<?php

namespace App\Controller\Client;

use App\Entity\Client;
use App\Manager\ClientManager;
use App\Manager\MemberManager;
use App\Manager\ParameterManager;
use App\Repository\ClientRepository;
use App\Utils\Enum\ClientType;
use App\Utils\Enum\MemberRoleEnum;
use App\Utils\Enum\SymfonyRole;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class CreateClientController extends AbstractController {

    private EntityManagerInterface $manager;
    private MemberManager $memberManager;
    private ParameterManager $parameterManager;
    private ClientManager $clientManager;
    private ClientRepository $clientRepository;
    private UserPasswordHasherInterface $userPasswordHasher;

    public function __construct(ClientRepository $clientRepository, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $manager) {
        $this->manager = $manager;
        $this->clientRepository = $clientRepository;
        $this->userPasswordHasher = $userPasswordHasher;
        $this->memberManager = new MemberManager($this->manager);
        $this->parameterManager = new ParameterManager($this->manager);
        $this->clientManager = new ClientManager($this->manager);
    }

    #[Route("/admin/client/create", name: "createClient", methods: ["GET", "POST"])]
    public function index(Request $request): Response {
        if (!$this->isGranted(SymfonyRole::SECRETAIRE)) {
            return $this->redirectToRoute('home');
        }

        $data = $request->request;
        $user = $this->clientRepository->findOneBy(["login" => $this->getUser()->getUserIdentifier()]);
        $assosRoles = $this->memberManager->getLowerOrEqualAssociationRole($user);
        $parameter = $this->parameterManager->getParameter();
        $message = "";

        if ($data->count() > 0) {
            $client = new Client();
            $this->clientManager->setData($client, $this->userPasswordHasher, $data->get("name"),
                                          $data->get("firstName"), $data->get("login"), $data->get("password"),
                                          $data->get("balance"), $data->get("clientType"), $data->get("assosRoles"), 0);

            if ($this->clientManager->verifyClient($client) && $this->clientManager->verifyPassword($data->get("password"))) {
                if ($this->clientManager->clientExists($client)) {
                    $message = "Ce client existe déjà";
                } else {
                    $this->clientManager->persist($client);

                    /*
                     * if the client added is a member, we have to add him in association table too.
                     *
                     * ->we didn't do a trigger because we don't have to role to insert it in assosciation table
                     *   so we have to get it from the data variable.
                     * */
                    if ($client->getClientType() == ClientType::ASSOCIATION) {

                        $newMember = $this->memberManager->makeMember($client, MemberRoleEnum::from($request->get("assosRoles")));

                        if ($newMember->getRole() == MemberRoleEnum::PRESIDENT) {
                            $this->memberManager->removeOtherPresidents($newMember);
                        }
                        $this->manager->persist($newMember);
                        $this->manager->flush();
                    }
                    return $this->redirectToRoute('menuClient', [
                        "message" => "Ajout avec succès"
                    ]);
                }
            } else {
                $message = "Merci de vérifier votre saisie";
            }
        }

        return $this->render('client/CreateClient.html.twig', [
            "parameter" => $parameter,
            "assosRoles" => $assosRoles,
            "message" => $message,
            "clientTypes" => $this->clientManager->getLowerOrEqualClientTypes($user)
        ]);
    }
}