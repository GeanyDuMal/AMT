<?php

namespace App\Controller;

use App\Entity\Client;
use App\Manager\ClientManager;
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
        $inputParameterBag = $request->request;
        $clientManager = new ClientManager($manager);
        $client = new Client();
        $flush = false;
        $verifPassword = "";

        //Permet d'eviter le bug de lka variable null a la premiere entrée sur la page
        if (!is_null($inputParameterBag->get("name"))){
            $client->setName($inputParameterBag->get("name"))
                    ->setFirstName($inputParameterBag->get("firstName"))
                    ->setLogin($inputParameterBag->get("login"))
                    ->setPassword($inputParameterBag->get("password"));
            $verifPassword = $inputParameterBag->get("confirmPassword");
        }

        /**
         * Si le form n'est pas vide, que le login n'existe pas et que les infos sont correctes alors on l'insere
         */
        if (!$clientManager->isEmpty($client) && !$clientManager->loginExists($client) && $clientManager->dataCorrect($client))
        {
            $clientManager->persist($client);
            $flush = true;
        }


        return $this->render('connexion/sign_in/index.html.twig', [
            'flush' => $flush
        ]);
    }
}
