<?php

namespace App\Controller;

use App\Entity\Client;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SignInController extends AbstractController
{
    /**
     * @Route("/sign_in", name="sign_in")
     */
    public function index(): Response
    {
        $client = new Client;

        $formNewClient = $this->createFormBuilder($client)
                            ->add("name")
                            ->add("firstName")
                            ->add("login")
                            ->add("password", PasswordType::class)
                            ->getForm();

        return $this->render('connexion/sign_in/index.html.twig', [
            'formCreationClient' => $formNewClient->createView(),
        ]);
    }
}
