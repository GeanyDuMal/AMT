<?php

namespace App\Controller\Connexion;

use App\Entity\Client;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProfileController extends AbstractController
{
    /**
     * @Route("/profile", name="profile")
     */
    public function index(EntityManagerInterface $manager): Response
    {
        if ($this->isGranted('IS_AUTHENTICATED_FULLY')){
            $clientRepository = $manager->getRepository(Client::class);

            $client = $clientRepository->findOneBy(["login" => $this->getUser()->getUsername()]);

            return $this->render('connexion/profile/index.html.twig', [
                "user" => $client,
            ]);
        }else{
            return $this->redirectToRoute("login");
        }
    }
}
