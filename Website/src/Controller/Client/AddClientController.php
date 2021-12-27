<?php

namespace App\Controller\Client;

use App\Entity\Client;
use App\Manager\ClientManager;
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
    public function index(UserPasswordHasherInterface $passwordHasher,Request $request,ClientTypeRepository $clientTypeRepository,EntityManagerInterface $manager,ValidatorInterface $validator): Response
    {
        if (!$this->isGranted('ROLE_PRESIDENT')){
            return $this->redirectToRoute('home');
        }
        $data = $request->request;
        $clientManager = new ClientManager($manager);
        $client = new Client();
        $error = "";
        $errors="";
        $types = $clientTypeRepository->findAll();
        if ($data->count()> 0) {
            $clientManager->setData($client,$request,$clientTypeRepository,$passwordHasher);
            $errors = $validator->validate($client);
            if($errors->count()==0)
                if($clientManager->loginExists($client)){
                    $error="Login Existe déja";
                }
                else{
                    $clientManager->persist($client);
                    return $this->redirectToRoute('client_list_message',["message"=>"Ajout avec succès"]);
                }
        }

        return $this->render('client/AddModalClient.html.twig',
            [
                'types' => $types,
                'errors' => $errors,
                'error' => $error,
                'client' => $client
            ]
        );
    }
}