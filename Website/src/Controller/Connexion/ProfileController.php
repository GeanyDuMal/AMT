<?php

namespace App\Controller\Connexion;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class ProfileController extends AbstractController
{
    /**
     * @Route("/profil", name="profil")
     */
    public function index(): Response
    {
        if ($this->isGranted('ROLE_USER')){

            $this->getUser()->getUsername();

            return $this->render('connexion/profil/index.html.twig', [
                "name" => $this->getUser()->getUsername(),
                'controller_name' => 'ProfileController',
            ]);
        }else{
            return $this->redirectToRoute("login");
        }
    }
}
