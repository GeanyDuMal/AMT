<?php

namespace App\Controller\Connexion;

use App\Manager\ClientManager;
use App\Repository\ClientRepository;
use App\Utils\Enum\SymfonyRole;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class DeleteProfileController extends AbstractController
{

    /**
     * @Route("/profile/delete", name="delete_profile", methods={"GET", "DELETE"})
     */
    public function index(EntityManagerInterface $manager, ClientRepository $clientRepository, TokenStorageInterface $tokenStorage,
        Request $request)
    {
        // Not allowed to remove you account if you are the president
        if ($this->isGranted(SymfonyRole::PRESIDENT) || !$this->isGranted('IS_AUTHENTICATED_FULLY')) {
            return $this->redirectToRoute('home');
        }

        $clientManager = new ClientManager($manager);
        $client = $clientRepository->findOneBy(["login" => $this->getUser()->getUserIdentifier()]);

        $clientManager->remove($client);

        // Supprime toutes les informations de la session (utilisateur connecté par exemple)
        $request->getSession()->invalidate();
        $tokenStorage->setToken();

        return $this->redirectToRoute("home");
    }
}