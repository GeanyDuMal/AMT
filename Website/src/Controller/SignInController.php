<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SignInController extends AbstractController
{
    /**
     * @Route("/sign_in", name="sign_in")
     */
    public function index(): Response
    {
        return $this->render('connexion/sign_in/index.html.twig', [
            'controller_name' => 'SignInController',
        ]);
    }
}
