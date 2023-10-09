<?php

namespace App\Controller\Connexion;

use App\Manager\ClientManager;
use App\Manager\ParameterManager;
use App\Repository\ClientRepository;
use App\Utils\Exception\ApplicationException;
use Doctrine\ORM\EntityManagerInterface;
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
    #[Route("/profile", name: "profile", methods: ["GET", "POST"])]
    public function index(Request          $request, UserPasswordHasherInterface $passwordHasher,
                          ClientRepository $clientRepository, EntityManagerInterface $manager): Response
    {
        if ($this->isGranted('IS_AUTHENTICATED_FULLY')) {
            $parameterManager = new ParameterManager($manager);
            $parameter = $parameterManager->getParameter();
            $client = $clientRepository->findOneBy(["login" => $this->getUser()->getUserIdentifier()]);
            $clientManager = new ClientManager($manager);
            $edit = false;
            $fail = false;

            $inputParameterBag = $request->request;
            $oldPassword = trim($inputParameterBag->get("oldPassword"));
            $newPassword = trim($inputParameterBag->get("newPassword"));
            $confirmPassword = trim($inputParameterBag->get("confirmPassword"));


            // Verifie que les champs soient bien rempli et que le nouveau mot de passe et la confirmation soient différent
            if ($oldPassword && $newPassword && ($newPassword === $confirmPassword) && !($oldPassword === $newPassword) &&
                $clientManager->verifyPassword($newPassword)) {
                // Verifie que l'ancien mot de passe corresponde et que le nouveau soit correct
                if (password_verify($oldPassword, $this->getUser()->getPassword())) {
                    $hashedPassword = $passwordHasher->hashPassword($client, $newPassword);

                    $client->setPassword($hashedPassword);

                    $clientManager->persist($client);
                    $edit = true;
                } else {
                    $fail = true;
                }
            }

            return $this->render('connexion/Profile.html.twig', [
                "parameter" => $parameter,
                "user" => $client,
                "edit" => $edit,
                "fail" => $fail
            ]);
        } else {
            return $this->redirectToRoute("login");
        }
    }

    /**
     * @Route("/logout", name="logout", methods={"GET"})
     * @throws ApplicationException
     */
    public function logout(): void {
        throw new ApplicationException('Fail to Logout');
    }
}
