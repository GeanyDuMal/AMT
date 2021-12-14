<?php

namespace App\Controller;

use App\Entity\Client;
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
        $inputParameterBag = $request->request;

        $client = new Client();

        //Permet d'eviter le bug de lka variable null a la premiere entrée sur la page
        if (!is_null($inputParameterBag->get("name"))){
            $client->setName($inputParameterBag->get("name"))
                    ->setFirstName($inputParameterBag->get("firstName"))
                    ->setLogin($inputParameterBag->get("login"))
                    ->setPassword($inputParameterBag->get("password"));
        }



        $flush = false;

        return $this->render('connexion/sign_in/index.html.twig', [
            'flush' => $flush
        ]);
    }
}
