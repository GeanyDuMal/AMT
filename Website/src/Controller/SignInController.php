<?php

namespace App\Controller;

use App\Entity\Client;
use Doctrine\ORM\EntityManager;
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
        $client = new Client;

        $flush = false;
        $formNewClient = $this->createFormBuilder($client)
                            ->add("name")
                            ->add("firstName")
                            ->add("login")
                            ->add("password", PasswordType::class)
                            ->getForm();

        $formNewClient->handleRequest($request);

        if($formNewClient->isSubmitted() && $formNewClient->isValid()){
            $manager->persist($client);
            $manager->flush();
            $flush = true;

        }


        return $this->render('connexion/sign_in/index.html.twig', [
            'formCreationClient' => $formNewClient->createView(),
            'flush' => $flush
        ]);
    }
}
