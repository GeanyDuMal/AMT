<?php

namespace App\Controller\Connexion;

use App\Entity\Client;
use App\Manager\ClientManager;
use App\Utils\Enum\ClientType;
use App\Utils\Enum\SymfonyRole;
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
        // Redirige vers le profil si deja connecté
        if ($this->isGranted('IS_AUTHENTICATED_FULLY')) {
            return $this->redirectToRoute('profile');
        }

        $inputParameterBag = $request->request;
        $clientManager = new ClientManager($manager);
        $client = new Client;
        $message = "";

        //Permet d'eviter le bug de la variable null a la premiere entrée sur la page
        if (!is_null($inputParameterBag->get("name"))) {

            $clientManager->setData($client, $passwordHasher, strtoupper(trim($inputParameterBag->get("name"))),
                trim($inputParameterBag->get("firstName")), trim($inputParameterBag->get("login")),
                trim($inputParameterBag->get("password")), 0, ClientType::ETUDIANT, null,
                0);

            $confirmPassword = trim($inputParameterBag->get("confirmPassword"));

            /*
             * Si le form n'est pas vide,
             * que le client n'existe pas
             * et que les infos sont correctes
             * alors on l'insere dans la base de données
             * La confirmation du mdp ne peux pas etre verif avec $client car son password est hashé
             */
            if ($clientManager->verifyClient($client) && !$clientManager->clientExists($client)
                && (trim($inputParameterBag->get("password")) == $confirmPassword)
                && $clientManager->verifyPassword($inputParameterBag->get("password"))) {

                $clientManager->persist($client);
                return $this->redirectToRoute('login');
            } else if ($clientManager->clientExists($client)) {
                $message = "Ce client existe déjà";
            } else if (!$clientManager->verifyPassword($inputParameterBag->get("password"))) {
                $message = "Votre mot de passe ne respecte pas les règles imposés";
            } else {
                $message = "Merci de vérifier votre saisie";
            }
        }

        return $this->render('connexion/Signin.html.twig', [
            "message" => $message
        ]);
    }
}
