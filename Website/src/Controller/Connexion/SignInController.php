<?php

namespace App\Controller\Connexion;

use App\Entity\Client;
use App\Manager\ClientManager;
use App\Repository\ClientTypeRepository;
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
    public function index(Request $request, EntityManagerInterface $manager, UserPasswordHasherInterface $passwordHasher, ClientTypeRepository $clientTypeRepository): Response
    {
        // Redirige vers le profil si deja connecté
        if ($this->isGranted('IS_AUTHENTICATED_FULLY')){
            return $this->redirectToRoute('profile');
        }

        $inputParameterBag = $request->request;
        $clientManager = new ClientManager($manager);
        $client = new Client;
        $loginExist = false;

        //Permet d'eviter le bug de la variable null a la premiere entrée sur la page
        if (!is_null($inputParameterBag->get("name"))){
            $hashedPassword = $passwordHasher->hashPassword($client, trim($inputParameterBag->get("password")));

            $name = $inputParameterBag->get("name");
            $firstName = $inputParameterBag->get("firstName");
            $login = $inputParameterBag->get("login");
            $balance = 0;
            $roleAssociationName = $inputParameterBag->get("none");
            $clientTypeName = $inputParameterBag->get("Etudiant");




            /*$client->setName(strtoupper(trim($inputParameterBag->get("name"))))
                ->setFirstName(trim($inputParameterBag->get("firstName")))
                ->setLogin(trim($inputParameterBag->get("login")))
                ->setPassword($hashedPassword)
                ->setClientType($clientTypeRepository->findOneBy(["name" => "Etudiant"]))
                ->setRoles(["ROLE_USER"])
                ->setFidelityPoint(0)
                ->setBalance(0);*/

            $clientManager->setData($client, $clientTypeRepository, $passwordHasher, $name,
                $firstName, $login, $hashedPassword, $balance, $roleAssociationName, $clientTypeName);

            $verifPassword = trim($inputParameterBag->get("confirmPassword"));

            /*
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
            }else if ($clientManager->loginExists($client)) {
                $loginExist = true;
            }
        }

        return $this->render('connexion/signin.html.twig', [
            "loginExist" => $loginExist
        ]);
    }
}
