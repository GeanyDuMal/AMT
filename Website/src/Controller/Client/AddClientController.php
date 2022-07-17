<?php

namespace App\Controller\Client;

use App\Entity\Client;
use App\Manager\AssociationManager;
use App\Manager\ClientManager;
use App\Repository\ClientRepository;
use App\Utils\Enum\AssociationRole;
use App\Utils\Enum\ClientType;
use App\Utils\Enum\SymfonyRole;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class AddClientController extends AbstractController
{
    /**
     * @Route("/admin/client/new", name="new_client",methods={"GET", "POST"} )
     */
    public function index(ClientRepository $clientRepository, UserPasswordHasherInterface $passwordHasher, Request $request,
                          EntityManagerInterface $manager, ValidatorInterface $validator): Response
    {
        if (!$this->isGranted(SymfonyRole::PRESIDENT)){
            return $this->redirectToRoute('home');
        }

        $data = $request->request;
        $assosRoles = AssociationRole::getAll();
        $errorLoginExist = "";
        $validationErrors = "";

        if ($data->count() > 0) {
            $clientManager = new ClientManager($manager);
            $associationManager = new AssociationManager($manager);
            $client = new Client();

            $clientManager->setData($client, $passwordHasher, $data->get("name"),
                $data->get("firstName"), $data->get("login"), $data->get("password"),
                $data->get("balance"), $data->get("assosRoles"),  $data->get("clientType"), 0);

            $validationErrors = $validator->validate($client);

            if($validationErrors->count() == 0)
                if($clientManager->loginExists($client)){
                    $errorLoginExist = "Login Existe déja";
                }else{
                    $clientManager->persist($client);

                    /*
                     * if the client added is a member, we have to add him in association table too.
                     *
                     * ->we didn't do a trigger because we don't have to role to insert it in assosciation table
                     *   so we have to get it from the data variable.
                     * */
                    if($client->getClientType() == ClientType::ASSOCIATION){

                        $newMember = $clientManager->makeMember($client, $request->get('assosRoles'));

                        if($newMember->getRole() == AssociationRole::PRESIDENT){
                            $associationManager->removeOtherPresidents($manager, $newMember, $clientRepository);
                        }
                        $manager->persist($newMember);
                        $manager->flush();
                    }
                    return $this->redirectToRoute('client_list',[
                        "message" => "Ajout avec succès"
                    ]);
                }
        }

        return $this->render('client/AddModalClient.html.twig', [
                'assosRoles' => $assosRoles,
                'validationErrors' => $validationErrors,
                'errorLoginExist' => $errorLoginExist
        ]);
    }
}