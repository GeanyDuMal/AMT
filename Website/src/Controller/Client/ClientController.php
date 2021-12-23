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
        $errors="";
        $types = $clientTypeRepository->findAll();
        if ($data->count()> 0) {
            $this->setData($client,$request,$clientTypeRepository);
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
    /**
     * @Route("/admin/client/edit/{id}", name="edit_client",methods={"GET", "POST"} )
     */
    public function editClientAction(Request $request,ClientTypeRepository $clientTypeRepository,ClientRepository $clientRepository,$id,EntityManagerInterface $manager,ValidatorInterface $validator): Response
    {
        $client = $clientRepository->find($id);
        $data = $request->request;
        $clientManager = new ClientManager($manager);
        $error = "";
        $types = $clientTypeRepository->findAll();
        $errors="";
        if ($data->count()> 0) {
            $this->setData($client,$request,$clientTypeRepository);
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
    /**
     * @Route("/admin/client/{message}", name="client_list_message",methods={"GET", "POST"} )
     */
    public function show($message,EntityManagerInterface $manager): Response
    {
        $clients = $manager->getRepository(Client::class)->findAll();
        return $this->render('client/index.html.twig', array('clients' => $clients,'message'=>$message));
    }
    private function getRoles(string $typeName):array{
        $role[]="ROLE_USER";
        switch ($typeName){
            case "Association":$role[] = "ROLE_ASSOC";break;
            case "Trésorier":array_push($role,"ROLE_ASSOC","ROLE_TRESORIER");break;
            case "Président":array_push($role,"ROLE_ASSOC","ROLE_TRESORIER","ROLE_PRESIDENT");break;
            default:break;
        }
        return $role;
    }
    private function setData(Client &$client,Request $request,ClientTypeRepository $clientTypeRepository){
        $data = $request->request;
        $client->setName($data->get('name'));
        $client->setFirstName($data->get('fname'));
        $client->setLogin($data->get('login'));
        /*
            if password input exists so it's the add page
            so we have to initialize the fidelity points
            and set the password to the chosen one.
        */
        if($data->get('pwd')){
            $client->setPassword($data->get('pwd'));
            $client->setFidelityPoint(0);
        }
        $client->setBalance($data->get('balance'));
        $typeName=$data->get('types');
        $role= $this->getRoles($typeName);
        $client->setRoles($role);
        $type=$clientTypeRepository->findOneBy(["name"=>$typeName]);
        $client->setClientType($type);
    }
}
