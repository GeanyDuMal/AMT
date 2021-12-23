<?php

namespace App\Controller\Client;

use App\Repository\ClientRepository;
use App\Repository\ClientTypeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Client;
use App\Entity\ClientType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use App\Manager\ClientManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ClientController extends AbstractController
{
    /**
     * @Route("/admin/client", name="client_list",methods={"GET", "POST"} )
     */
    public function index(EntityManagerInterface $manager): Response
    {
        $clients = $manager->getRepository(Client::class)->findAll();
        return $this->render('client/index.html.twig', array('clients' => $clients));
    }
    /**
     * @Route("/admin/client/new", name="new_client",methods={"GET", "POST"} )
     */
    public function addClientAction(Request $request,ClientTypeRepository $clientTypeRepository,EntityManagerInterface $manager,ValidatorInterface $validator): Response
    {
        $data = $request->request;
        $clientManager = new ClientManager($manager);
        $client = new Client();
        $error = "";
        $types = $clientTypeRepository->findAll();
        $errors="";
        if ($data->count()> 0) {
            $client->setName($data->get('name'));
            $client->setFirstName($data->get('fname'));
            $client->setLogin($data->get('login'));
            $client->setPassword($data->get('pwd'));
            $client->setBalance($data->get('balance'));
            $typeName=$data->get('types');
            $type=$clientTypeRepository->findOneBy(["name"=>$typeName]);
            $client->setClientType($type);
            $client->setFidelityPoint(0);
            $errors = $validator->validate($client);
            if($errors->count()==0)
                if($clientManager->loginExists($client)){
                    $error="Login Existe déja";
                }
                else{
                    $clientManager->persist($client);
                    return $this->redirectToRoute('client_list');
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
    /**
     * @Route("/admin/client/edit/{id}", name="edit_client",methods={"GET", "POST"} )
     */
    public function editClientAction(Request $request,ClientTypeRepository $clientTypeRepository,ClientRepository $clientRepository,$id,EntityManagerInterface $manager,ValidatorInterface $validator): Response
    {
        $clientSelected = $clientRepository->find($id);
        $client = $clientSelected;
        $data = $request->request;
        $clientManager = new ClientManager($manager);
        $error = "";
        $types = $clientTypeRepository->findAll();
        $errors="";
        if ($data->count()> 0) {
            $client->setName($data->get('name'));
            $client->setFirstName($data->get('fname'));
            $client->setLogin($data->get('login'));
            $client->setPassword($data->get('pwd'));
            $client->setBalance($data->get('balance'));
            $typeName=$data->get('types');
            $type=$clientTypeRepository->findOneBy(["name"=>$typeName]);
            $client->setClientType($type);
            $client->setFidelityPoint(0);
            $errors = $validator->validate($client);
            if($errors->count()==0)
                if(strcmp($clientSelected->getLogin(),$client->getLogin())!=0 &&
                    $clientManager->loginExists($client)){
                    $error="Login Existe déja";
                }
                else{
                    $clientManager->persist($client);
                    return $this->redirectToRoute('client_list');
                }
        }

        return $this->render('client/EditModalClient.html.twig',
            [
                'types' => $types,
                'errors' => $errors,
                'error' => $error,
                'client' => $client
            ]
        );
    }
    /**
     * @Route("/admin/client/delete/{id}", name="delete_client", methods="GET" )
     */
    public function deleteClientAction($id, EntityManagerInterface $manager): Response
    {
        $client = $manager->getRepository('App:Client')->find($id);
        $manager->remove($client);
        $manager->flush();
        $this->addFlash('message', 'Client supprimer avec succée');
        return $this->redirectToRoute('client_list');
    }
}
