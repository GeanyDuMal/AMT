<?php

namespace App\Controller\Client;

use App\Entity\Association;
use App\Entity\Client;
use App\Manager\ClientManager;
use App\Repository\AssociationRoleRepository;
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
    public function index(UserPasswordHasherInterface $passwordHasher,Request $request,AssociationRoleRepository $associationRoleRepository,ClientTypeRepository $clientTypeRepository,EntityManagerInterface $manager,ValidatorInterface $validator): Response
    {
        if (!$this->isGranted('ROLE_PRESIDENT')){
            return $this->redirectToRoute('home');
        }
        $commonFunctions=new CommonFunctions();
        $data = $request->request;
        $clientManager = new ClientManager($manager);
        $client = new Client();
        $errorLoginExist = "";
        $validationErrors="";
        $assosRoles = $associationRoleRepository->findAll();
        if ($data->count()> 0) {
            $commonFunctions->setData($client,$request,$clientTypeRepository,$passwordHasher);
            $validationErrors = $validator->validate($client);
            if($validationErrors->count()==0)
                if($clientManager->loginExists($client)){
                    $errorLoginExist="Login Existe déja";
                }
                else{
                    $clientManager->persist($client);
                    /*
                     * if the client added is a member, we have to add him in association table too.
                     * */
                    if($client->getClientType()->getName()=="Association"){
                        $newMember=$commonFunctions->makeMember($client,$associationRoleRepository,$request);
                        $manager->persist($newMember);
                        $manager->flush();
                    }

                    return $this->redirectToRoute('client_list_message',["message"=>"Ajout avec succès"]);
                }
        }

        return $this->render('client/AddModalClient.html.twig',
            [
                'assosRoles' => $assosRoles,
                'validationErrors' => $validationErrors,
                'errorLoginExist' => $errorLoginExist,
                'client' => $client
            ]
        );
    }
}