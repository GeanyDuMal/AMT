<?php

namespace App\Controller\Connexion;

use App\Manager\ClientManager;
use App\Repository\ClientRepository;
use App\Utils\Enum\SymfonyRole;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class DeleteProfileController extends AbstractController
{
    private EntityManagerInterface $manager;
    private ClientManager $clientManager;
    private ClientRepository $clientRepository;
    private TokenStorageInterface $tokenStorage;
    public function __construct(EntityManagerInterface $manager, TokenStorageInterface $tokenStorage, ClientRepository $clientRepository) {
        $this->manager = $manager;
        $this->clientManager = new ClientManager($this->manager);
        $this->clientRepository = $clientRepository;
        $this->tokenStorage = $tokenStorage;
    }

    #[Route("/profile/delete", name: "deleteProfile", methods: ["GET", "DELETE"])]
    public function index(Request $request): RedirectResponse
    {
        // Not allowed to remove you account if you are the president or if you aren't connected
        if ($this->isGranted(SymfonyRole::PRESIDENT) || !$this->isGranted('IS_AUTHENTICATED_FULLY')) {
            return $this->redirectToRoute('profile', [
                "message" => "Votre compte ne peut pas être supprimé, merci de contacter l'administrateur "
            ]);
        }

        $client = $this->clientRepository->findOneBy(["login" => $this->getUser()->getUserIdentifier()]);

        $this->clientManager->remove($client);

        // Supprime toutes les informations de la session (utilisateur connecté par exemple)
        $request->getSession()->invalidate();
        $this->tokenStorage->setToken();

        return $this->redirectToRoute("home");
    }
}