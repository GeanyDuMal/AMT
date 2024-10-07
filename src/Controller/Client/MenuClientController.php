<?php

namespace App\Controller\Client;

use App\Manager\ParameterManager;
use App\Repository\ClientRepository;
use App\Utils\Enum\SymfonyRole;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MenuClientController extends AbstractController
{
    private EntityManagerInterface $manager;
    private ParameterManager $parameterManager;
    private ClientRepository $clientRepository;

    public function __construct(ClientRepository $clientRepository, EntityManagerInterface $manager) {
        $this->manager = $manager;
        $this->parameterManager = new ParameterManager($this->manager);
        $this->clientRepository = $clientRepository;
    }

    #[Route("/admin/client/{message?}", name: "menuClient", methods: ["GET"])]
    public function show(?string $message): Response {
        if (!$this->isGranted(SymfonyRole::ASSOC)) {
            return $this->redirectToRoute('home');
        }

        $parameter = $this->parameterManager->getParameter();
        $clients = $this->clientRepository->findAll();

        return $this->render("client/MenuClient.html.twig", [
            "parameter" => $parameter,
            "clients" => $clients,
            "message" => $message
        ]);
    }

}
