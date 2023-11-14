<?php

namespace App\Controller\Client;

use App\Manager\ClientManager;
use App\Manager\ParameterManager;
use App\Repository\ClientRepository;
use App\Utils\Enum\SymfonyRole;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SearchClientController extends AbstractController
{
    #[Route("/client/ajax/searchClient", name: "ajaxSearchClient", methods: ["GET"])]
    public function show(EntityManagerInterface $manager, Request $request): Response {
        if (!$this->isGranted(SymfonyRole::ASSOC)) {
            return $this->json([]);
        }

        $clientManager = new ClientManager($manager);
        $clientDto = $clientManager->getClientByNameFirstName($request->query->get("researchString"));

        return $this->json($clientDto);
    }

}
