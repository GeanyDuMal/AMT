<?php

namespace App\Controller\Client;

use App\Manager\ClientManager;
use App\Manager\ParameterManager;
use App\Repository\ClientRepository;
use App\Utils\Enum\SymfonyRoleEnum;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SearchClientController extends AbstractController
{
    private EntityManagerInterface $manager;
    private ClientManager $clientManager;

    public function __construct(EntityManagerInterface $manager) {
        $this->manager = $manager;
        $this->clientManager = new ClientManager($this->manager);
    }

    #[Route("/client/ajax/searchClient", name: "ajaxSearchClient", methods: ["GET"])]
    public function show(Request $request): Response {
        if (!$this->isGranted(SymfonyRoleEnum::ASSOC->value)) {
            return $this->json([]);
        }

        $clientDto = $this->clientManager->getClientByNameFirstName($request->query->get("researchString"));

        return $this->json($clientDto);
    }

}
