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
        $loginExist = false;

        //Permet d'eviter le bug de la variable null a la premiere entrée sur la page
        if (!is_null($inputParameterBag->get("name"))) {
            $hashedPassword = $passwordHasher->hashPassword($client, trim($inputParameterBag->get("password")));

            $clientManager->setData($client, $passwordHasher, strtoupper(trim($inputParameterBag->get("name"))),
                trim($inputParameterBag->get("firstName")), trim($inputParameterBag->get("login")),
                trim($inputParameterBag->get("password")), 0, null, ClientType::ETUDIANT,
                0);

            $verifPassword = trim($inputParameterBag->get("confirmPassword"));

            /*
             * Si le form n'est pas vide,
             * que le login n'existe pas
             * et que les infos sont correctes
             * alors on l'insere dans la base de donnée
             * La confirmation du mdp ne peux pas etre verif avec $client car son password est hashé
             */
            if (!$clientManager->isNotFull($client) && !$clientManager->loginExists($client)
                && $clientManager->dataCorrect($client) && (trim($inputParameterBag->get("password")) == $verifPassword)) {
                $clientManager->persist($client);

                return $this->redirectToRoute('login');
            } else if ($clientManager->loginExists($client)) {
                $loginExist = true;
            }
        }

        return $this->render('connexion/Signin.html.twig', [
            "loginExist" => $loginExist
        ]);
    }
}
