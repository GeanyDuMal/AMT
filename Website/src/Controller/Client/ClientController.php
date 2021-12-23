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
    public function addClientAction(Request $request, EntityManagerInterface $manager,ValidatorInterface $validator): Response
    {
        $data = $request->request;
        $clientManager = new ClientManager($manager);
        $client = new Client();
        $error = "";
        if ($data->count()> 0) {
            $client->setName($data->get('name'));
            $client->setFirstName($data->get('fname'));
            $client->setLogin($data->get('login'));
            $client->setPassword($data->get('pwd'));
            $client->setBalance($data->get('balance'));
            $errors = $validator->validate($client);

            return $this->redirectToRoute('new_client',
                [
                    'errors' => $errors
                ]
            );
            return new Response($errors);
        }
        $types = $this->getDoctrine()->getRepository(ClientType::class)->findAll();
        return $this->render('client/AddModalClient.html.twig',
            [
                'types' => $types
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
