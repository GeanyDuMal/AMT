<?php

namespace App\Controller\Connexion;

use App\Entity\Client;
use App\Manager\ClientManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SignInController extends AbstractController
{
    /**
     * @Route("/signin", name="signin")
     */
    public function index(Request $request, EntityManagerInterface $manager): Response
    {
        $inputParameterBag = $request->request;
        $clientManager = new ClientManager($manager);
        $client = new Client();
        $flush = false;
        $verifPassword = "";

        //Permet d'eviter le bug de la variable null a la premiere entrée sur la page
        if (!is_null($inputParameterBag->get("name"))){
            $client->setName(trim($inputParameterBag->get("name")))
                    ->setFirstName(trim($inputParameterBag->get("firstName")))
                    ->setLogin(trim($inputParameterBag->get("login")))
                    ->setPassword(trim($inputParameterBag->get("password")));
            $verifPassword = trim($inputParameterBag->get("confirmPassword"));
        }

        /**
         * Si le form n'est pas vide, 
         * que le login n'existe pas 
         * et que les infos sont correctes 
         * alors on l'insere dans la base de donnée
         */
        if (!$clientManager->isNotFull($client) && !$clientManager->loginExists($client)
            && $clientManager->dataCorrect($client) && $client->getPassword() == $verifPassword)
        {
            $clientManager->persist($client);
            $flush = true;
        }


        return $this->render('connexion/signin/index.html.twig', [
            'flush' => $flush
        ]);
    }
}
