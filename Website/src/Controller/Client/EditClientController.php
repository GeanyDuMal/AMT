<?php

namespace App\Controller\Client;
use App\Manager\ClientManager;
use App\Repository\AssociationRepository;
use App\Repository\AssociationRoleRepository;
use App\Repository\ClientRepository;
use App\Repository\ClientTypeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class EditClientController extends AbstractController
{
    /**
     * @Route("/admin/client/edit/{id}", name="edit_client",methods={"GET", "POST"} )
     */
    public function index(UserPasswordHasherInterface $passwordHasher,AssociationRepository $associationRepository,Request $request,ClientTypeRepository $clientTypeRepository,AssociationRoleRepository $associationRoleRepository,ClientRepository $clientRepository,$id,EntityManagerInterface $manager,ValidatorInterface $validator): Response
    {
        if (!$this->isGranted('ROLE_PRESIDENT')){
            return $this->redirectToRoute('home');
        }
        $client = $clientRepository->find($id);
        $data = $request->request;
        $clientManager = new ClientManager($manager);
        $error = "";
        $types = $associationRoleRepository->findAll();
        $errors="";
        if ($data->count()> 0) {
            $clientManager->setData($client,$request,$clientTypeRepository,$passwordHasher);
            $errors = $validator->validate($client);
            if($errors->count()==0)
                if(strcmp($clientRepository->find($id)->getLogin(),$client->getLogin())!=0 &&
                    $clientManager->loginExists($client)){
                    $error="Login Existe déja";
                }
                else{
                    $clientManager->persist($client);
                    $request->query->get("Modification avec succés");
                    return $this->redirectToRoute('client_list_message',["message"=>"Modification avec succés"]);
                }
        }
        $member=$associationRepository->findOneBy(["member"=>$client]);
        return $this->render('client/EditModalClient.html.twig',
            [
                'types' => $types,
                'errors' => $errors,
                'error' => $error,
                'client' => $client,
                'member' => $member
            ]
        );
    }
}