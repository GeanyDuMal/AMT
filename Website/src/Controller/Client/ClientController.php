<?php

namespace App\Controller\Client;

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

class ClientController extends AbstractController
{
    /**
     * @Route("/admin/client", name="client_list",methods={"GET", "POST"} )
     */
    public function index(): Response
    {
        $clients = $this->getDoctrine()->getRepository(Client::class)->findAll();
        return $this->render('client/index.html.twig', array('clients' => $clients));
    }
    /**
     * @Route("/admin/client/new", name="new_client",methods={"GET", "POST"} )
     */
    public function addClientAction(Request $request, EntityManagerInterface $manager): Response
    {
        $data = $request->request;
        $clientManager = new ClientManager($manager);
        $client = new Client();
        $error = "";
        if ($data->count() > 0) {
            $client->setName($data->get('name'));
            $client->setFirstName($data->get('fname'));
            $client->setLogin($data->get('login'));
            $client->setPassword($data->get('pwd'));
            if ($data->get('balance') == "")
                $client->setBalance(0);
            else
                $client->setBalance($data->get('balance'));
            if (!$clientManager->isNotFull($client)) {
                $error = "Les champs ne doit pas etre null!";
            } elseif (!$clientManager->dataCorrect($client)) {
                $error = "Vérifier vos champs!";
            } elseif ($clientManager->loginExists($client)) {
                $error = "Ce login existe déja!";
            } else if (strcmp($data->get('confPwd'), $client->getPassword()) != 0)
                $error = "Vérifier la confirmation de votre mot de passe!";
        }
        $this->addFlash("error", $error);
        $types = $this->getDoctrine()->getRepository(ClientType::class)->findAll();
        return $this->render(
            'client/AddModalClient.html.twig',
            [
                'types' => $types,
                'client' => $client
            ]
        );
    }
    /**
     * @Route("/admin/client/edit/{id}", name="edit_client",methods={"GET", "POST"} )
     */
    public function editClientAction(): Response
    {
        $client = new Client();
        return $this->render('client/EditModalClient.html.twig');
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
