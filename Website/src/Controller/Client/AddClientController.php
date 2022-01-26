<?php

namespace App\Controller\Client;

use App\Entity\Client;
use App\Manager\AssociationManager;
use App\Manager\ClientManager;
use App\Repository\AssociationRoleRepository;
use App\Repository\ClientRepository;
use App\Repository\ClientTypeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Routing\Annotation\Route;

class AddClientController extends AbstractController
{
    /**
     * @Route("/admin/client/new", name="new_client",methods={"GET", "POST"} )
     */
    public function index(ClientRepository $clientRepository, UserPasswordHasherInterface $passwordHasher, Request $request,
                          AssociationRoleRepository $associationRoleRepository, ClientTypeRepository $clientTypeRepository,
                          EntityManagerInterface $manager, ValidatorInterface $validator): Response
    {
        if (!$this->isGranted('ROLE_PRESIDENT')){
            return $this->redirectToRoute('home');
        }

        $data = $request->request;
        $assosRoles = $associationRoleRepository->findAll();
        $errorLoginExist = "";
        $validationErrors = "";

        if ($data->count()> 0) {
            $clientManager = new ClientManager($manager);
            $associationManager = new AssociationManager($manager);
            $client = new Client();

            $clientManager->setData($client, $clientTypeRepository, $passwordHasher, $data->get("name"),
                $data->get("firstName"), $data->get("login"), $data->get("password"),
                $data->get("balance"), $data->get("assosRoles"),  $data->get("clientType"));

            $validationErrors = $validator->validate($client);

            if($validationErrors->count()==0)
                if($clientManager->loginExists($client)){
                    $errorLoginExist="Login Existe déja";
                }
                else{
                    $clientManager->persist($client);
                    /*
                     * if the client added is a member, we have to add him in association table too.
                     *
                     * ->we didn't do a trigger because we don't have to role to insert it in assosciation table
                     *   so we have to get it from the data variable.
                     * */
                    if($client->getClientType()->getName() == "Association"){
                        $roleName = $request->get('assosRoles');
                        $roleAssociation = $associationRoleRepository->findOneBy(["name" => $roleName]);

                        $newMember = $clientManager->makeMember($client, $roleAssociation);

                        if($newMember->getRole()->getName() == "President"){
                            $associationManager->removeOtherPresidents($manager, $newMember, $clientTypeRepository, $clientRepository);
                        }
                        $manager->persist($newMember);
                        $manager->flush();
                    }
                    return $this->redirectToRoute('client_list',[
                        "message"=>"Ajout avec succès"
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