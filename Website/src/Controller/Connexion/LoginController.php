<?php

namespace App\Controller\Connexion;

use App\Entity\Client;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class LoginController extends AbstractController
{
    /**
     * @Route("/login", name="login")
     *
     * go to :
     * https://symfony.com/doc/5.4/security.html#authenticating-users
     */
    public function index(AuthenticationUtils $authenticationUtils): Response
    {
        // Redirige vers le profil si deja connecté
        if ($this->isGranted('IS_AUTHENTICATED_FULLY')){
            return $this->redirectToRoute('profile');
        }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();

        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('connexion/login/index.html.twig', [
            'lastUsername' => $lastUsername,
            'error' => $error,
        ]);
    }
}
