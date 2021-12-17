<?php

namespace App\Controller\Connexion;

use App\Entity\Client;
use App\Manager\ClientManager;
use App\Security\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class SignInController extends AbstractController
{
    /**
     * @Route("/signin", name="signin")
     */
    public function index(Request $request, EntityManagerInterface $manager, UserPasswordHasherInterface $passwordHasher): Response
    {

        $inputParameterBag = $request->request;
        $clientManager = new ClientManager($manager);
        $client = new Client();
        $verifPassword = "";

        //Permet d'eviter le bug de la variable null a la premiere entrée sur la page
        if (!is_null($inputParameterBag->get("name"))){
            $hashedPassword = $passwordHasher->hashPassword(
                $client,
                trim($inputParameterBag->get("password")));

            $hashedPassword2 = $passwordHasher->hashPassword(
                $client,
                trim($inputParameterBag->get("password")));

            $client->setName(trim($inputParameterBag->get("name")))
                    ->setFirstName(trim($inputParameterBag->get("firstName")))
                    ->setLogin(trim($inputParameterBag->get("login")))
                    ->setPassword($hashedPassword);
            $verifPassword = trim($inputParameterBag->get("confirmPassword"));

            dump($hashedPassword);
            dump($hashedPassword2);
        }



        /**
         * Si le form n'est pas vide, 
         * que le login n'existe pas 
         * et que les infos sont correctes 
         * alors on l'insere dans la base de donnée
         * La confirmation du mdp ne peux pas etre verif avec $client car son password est hashé
         */
        if (!$clientManager->isNotFull($client) && !$clientManager->loginExists($client)
            && $clientManager->dataCorrect($client) && (trim($inputParameterBag->get("password")) == $verifPassword))
        {
            $clientManager->persist($client);

            return $this->redirectToRoute('login');
        }else{
            return $this->render('connexion/signin/index.html.twig', []);
        }
    }
}
