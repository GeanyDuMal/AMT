<?php

namespace App\Controller;

use App\Entity\Client;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SignInController extends AbstractController
{
    /**
     * @Route("/sign_in", name="sign_in")
     */
    public function index(Request $request, EntityManagerInterface $manager): Response
    {
        /** Voir qu'est ce qui doit etre fait dans le controller et dans le manager
         *  A voir si l'on maintient le form coté symfony ou si l'on passe a un form PHP
         *  Moins opti mais bcp plus simple et esthetique coté front
         */
        $client = new Client;
        $clientRepository = $manager->getRepository(Client::class);
        $flush = false;

        $formNewClient = $this->createFormBuilder($client)
                            ->add("name")
                            ->add("firstName")
                            ->add("login")
                            ->add("password", PasswordType::class)
                            ->getForm();

        $formNewClient->handleRequest($request);

        //Verif duplicata
        $data = $formNewClient->getData();

        $duplicata = $clientRepository->findOneBy(["login" => $data->getLogin()]);
        dump($duplicata);

        //Faire plus de verif et test que login n'existe pas 
        //Message dans le cas ou il y a une erreur
        if($formNewClient->isSubmitted() && $formNewClient->isValid() && $duplicata == NULL){
            $manager->persist($client);
            $manager->flush();
            $flush = true;

            //Redirection ? 
        }


        return $this->render('connexion/sign_in/index.html.twig', [
            'formCreationClient' => $formNewClient->createView(),
            'flush' => $flush
        ]);
    }
}
