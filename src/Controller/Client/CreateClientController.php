<?php

namespace App\Controller\Client;

use App\Entity\Client;
use App\Manager\AssociationManager;
use App\Manager\ClientManager;
use App\Repository\ClientRepository;
use App\Utils\Enum\MemberRole;
use App\Utils\Enum\ClientType;
use App\Utils\Enum\SymfonyRole;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class CreateClientController extends AbstractController
{
    /**
     * @Route("/admin/client/create", name="createClient", methods={"GET", "POST"} )
     */
    public function index(ClientRepository $clientRepository, UserPasswordHasherInterface $passwordHasher,
                          Request $request, EntityManagerInterface $manager): Response
    {
        if (!$this->isGranted(SymfonyRole::SECRETAIRE)){
            return $this->redirectToRoute('home');
        }

        $data = $request->request;
        $user = $clientRepository->findOneBy(["login" => $this->getUser()->getUserIdentifier()]);
        $associationManager = new AssociationManager($manager);
        $assosRoles = $associationManager->getLowerOrEqualAssociationRole($user);
        $message = "";

        if ($data->count() > 0) {
            $clientManager = new ClientManager($manager);
            $client = new Client();

            $clientManager->setData($client, $passwordHasher, $data->get("name"),
                $data->get("firstName"), $data->get("login"), $data->get("password"),
                $data->get("balance"), $data->get("clientType"), $data->get("assosRoles"), 0);
                
            if($clientManager->verifyClient($client) && $clientManager->verifyPassword($data->get("password"))) {
                if ($clientManager->clientExists($client)) {
                    $message = "Ce client existe déjà";
                } else {
                    $clientManager->persist($client);

                    /*
                     * if the client added is a member, we have to add him in association table too.
                     *
                     * ->we didn't do a trigger because we don't have to role to insert it in assosciation table
                     *   so we have to get it from the data variable.
                     * */
                    if ($client->getClientType() == ClientType::ASSOCIATION) {

                        $newMember = $associationManager->makeMember($client, $request->get('assosRoles'));

                        if($newMember->getRole() == MemberRole::PRESIDENT){
                            $associationManager->removeOtherPresidents($newMember);
                        }
                        $manager->persist($newMember);
                        $manager->flush();
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
            'assosRoles' => $assosRoles,
            'message' => $message,
            'clientTypes' => ClientType::getAll()
        ]);
    }
}