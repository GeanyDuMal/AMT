<?php

namespace App\Controller\Connexion;

use App\Entity\Client;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
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


            //dd(password_verify('kiki.', '$2y$13$yum1RBAT5kL12Am9jLRvxeaGoj2S4tSd8XBGvQKvTiuDMT3erPt7m'));
            //$this->isCsrfTokenValid()

            $client = $clientRepository->findOneBy(["login" => $this->getUser()->getUserIdentifier()]);

            return $this->render('connexion/profile/index.html.twig', [
                "user" => $client,
            ]);
        }else{
            return $this->redirectToRoute("login");
        }
    }

    /**
     * @Route("/logout", name="logout", methods={"GET"})
     * @throws Exception
     */
    public function logout(): void
    {
        // controller can be blank: it will never be called!
        throw new Exception('Don\'t forget to activate logout in security.yaml');
    }
}
