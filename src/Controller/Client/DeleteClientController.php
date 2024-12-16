<?php

namespace App\Controller\Client;

use App\Manager\ClientManager;
use App\Repository\ClientRepository;
use App\Utils\Enum\SymfonyRoleEnum;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Annotation\Route;

class DeleteClientController extends AbstractController
{
    private EntityManagerInterface $manager;
    private ClientManager $clientManager;
    private ClientRepository $clientRepository;

    public function __construct(EntityManagerInterface $manager, ClientRepository $clientRepository) {
        $this->manager = $manager;
        $this->clientManager = new ClientManager($this->manager);
        $this->clientRepository = $clientRepository;
    }

    #[Route("/admin/client/delete/{!id}", name: "deleteClient", methods: ["GET", "DELETE"])]
    public function index($id): RedirectResponse|JsonResponse {
        if (!$this->isGranted(SymfonyRoleEnum::SECRETAIRE->value)) {
            return $this->redirectToRoute('home');
        }

        $client = $this->clientRepository->find($id);

        if ($client) {
            $this->clientManager->remove($client);
            return $this->redirectToRoute("menuClient");
        } else {
            return $this->redirectToRoute("home");
        }
    }
}