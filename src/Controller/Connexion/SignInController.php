<?php

namespace App\Controller\Connexion;

use App\Entity\Client;
use App\Manager\ClientManager;
use App\Manager\ParameterManager;
use App\Utils\Enum\ClientType;
use App\Utils\Enum\SymfonyRole;
use App\Utils\Exception\ApplicationException;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class SignInController extends AbstractController
{

    #[Route("/signin", name: "signin", methods: ["GET", "POST"])]
    public function index(Request $request, EntityManagerInterface $manager, UserPasswordHasherInterface $passwordHasher): Response
    {
        // Redirige vers le profil si deja connecté
        if ($this->isGranted('IS_AUTHENTICATED_FULLY')) {
            return $this->redirectToRoute('profile');
        }

        $parameterManager = new ParameterManager($manager);
        $parameter = $parameterManager->getParameter();
        $data = $request->request;
        $clientManager = new ClientManager($manager);
        $client = new Client;
        $message = "";

        //Permet d'eviter le bug de la variable null a la premiere entrée sur la page
        if ($data->count() > 0) {

            $clientManager->setData($client, $passwordHasher, strtoupper(trim($data->get("name"))),
                trim($data->get("firstName")), trim($data->get("login")),
                trim($data->get("password")), 0, ClientType::ETUDIANT, null,
                0);

            $confirmPassword = trim($data->get("confirmPassword"));

            if (trim($data->get("password")) == trim($data->get("confirmPassword")) && $clientManager->verifyPassword(trim($data->get("password")))) {
                try {
                    $clientManager->persistClientIfNotExists($client);
                    return $this->redirectToRoute('login');
                } catch (ApplicationException $e) {
                    $message = $e->getCustomMessage();
                }
            } else {
                $message = "Merci de verifier les mots de passes";
            }
        }

        return $this->render('connexion/Signin.html.twig', [
            "parameter" => $parameter,
            "message" => $message
        ]);
    }
}
