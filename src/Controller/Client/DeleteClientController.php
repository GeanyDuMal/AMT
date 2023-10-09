<?php

namespace App\Controller\Client;

use App\Manager\ClientManager;
use App\Repository\ClientRepository;
use App\Utils\Enum\SymfonyRole;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Annotation\Route;

class DeleteClientController extends AbstractController
{

    #[Route("/admin/client/delete/{!id}", name: "deleteClient", methods: ["GET", "DELETE"])]
    public function index($id, EntityManagerInterface $manager, ClientRepository $clientRepository): RedirectResponse|JsonResponse {
        if (!$this->isGranted(SymfonyRole::SECRETAIRE)) {
            return $this->redirectToRoute('home');
        }

        $clientManager = new ClientManager($manager);
        $client = $clientRepository->find($id);

        if ($client) {
            $clientManager->remove($client);
            return $this->redirectToRoute("menuClient");
        } else {
            return $this->redirectToRoute("home");
        }
    }
}