<?php

namespace App\Controller\Connexion;

use App\Entity\Client;
use App\Manager\ClientManager;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class ProfileController extends AbstractController
{
    /**
     * @Route("/profile", name="profile")
     */
    public function index(Request $request, EntityManagerInterface $manager, UserPasswordHasherInterface $passwordHasher): Response
    {
        if ($this->isGranted('IS_AUTHENTICATED_FULLY')){
            $clientRepository = $manager->getRepository(Client::class);

            //$this->isCsrfTokenValid()

            $client = $clientRepository->findOneBy(["login" => $this->getUser()->getUserIdentifier()]);

            $inputParameterBag = $request->request;

            // Verifie que les champs soit bien rempli
            if (!is_null($inputParameterBag->get("oldPassword"))
                && !is_null($inputParameterBag->get("newPassword"))
                && trim($inputParameterBag->get("oldPassword")) === trim($inputParameterBag->get("newPassword")))
            {
                $clientManager = new ClientManager($manager);

                // Verifie que l'ancien mot de passe corresponde et que le nouveau soit correct
                if (password_verify(trim($inputParameterBag->get("oldPassword")), $this->getUser()->getPassword())
                    && $clientManager->verifPassword(trim($inputParameterBag->get("oldPassword"))))
                {
                    $hashedPassword = $passwordHasher->hashPassword(
                        $client,
                        trim($inputParameterBag->get("newPassword")));

                    $client->setPassword($hashedPassword);

                    $clientManager->persist($client);
                }
            }

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
