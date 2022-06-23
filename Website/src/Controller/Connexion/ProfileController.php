<?php

namespace App\Controller\Connexion;

use App\Manager\ClientManager;
use App\Repository\ClientRepository;
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
    public function index(Request $request, UserPasswordHasherInterface $passwordHasher,
        ClientRepository $clientRepository, ClientManager $clientManager): Response
    {
        if ($this->isGranted('IS_AUTHENTICATED_FULLY')){
            $client = $clientRepository->findOneBy(["login" => $this->getUser()->getUserIdentifier()]);
            $edit = false;

            $inputParameterBag = $request->request;

            // Verifie que les champs soient bien rempli et que le nouveau mot de passe et la confirmation soientt différent
            if (!is_null($inputParameterBag->get("oldPassword")) &&
                !is_null($inputParameterBag->get("newPassword")) &&
                trim($inputParameterBag->get("newPassword")) === trim($inputParameterBag->get("confirmPassword")) &&
                !($inputParameterBag->get("oldPassword") === ($inputParameterBag->get("newPassword"))))
            {
                // Verifie que l'ancien mot de passe corresponde et que le nouveau soit correct
                if (password_verify(trim($inputParameterBag->get("oldPassword")), $this->getUser()->getPassword())) {
                    $hashedPassword = $passwordHasher->hashPassword($client, trim($inputParameterBag->get("newPassword")));

                    $client->setPassword($hashedPassword);

                    $clientManager->persist($client);
                    $edit = true;
                }else{
                    $edit = 'wrong_password';
                }
            }

            return $this->render('connexion/profile.html.twig', [
                "user" => $client,
                "edit" => $edit
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
