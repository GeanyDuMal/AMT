<?php

namespace App\Controller\Connexion;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class LoginController extends AbstractController
{
    private AuthenticationUtils $authenticationUtils;

    public function __construct(AuthenticationUtils $authenticationUtils) {
        $this->authenticationUtils = $authenticationUtils;
    }

    #[Route("/login", name: "login", methods: ["GET", "POST"])]
    public function index(): Response
    {
        // Redirige vers le profil si deja connecté
        if ($this->isGranted('IS_AUTHENTICATED_FULLY')) {
            return $this->redirectToRoute('profile');
        }

        // get the login error if there is one
        $error = $this->authenticationUtils->getLastAuthenticationError();

        // last username entered by the user
        $lastLogin = $this->authenticationUtils->getLastUsername();

        return $this->render('connexion/Login.html.twig', [
            'lastLogin' => $lastLogin,
            'error' => $error,
        ]);
    }
}
