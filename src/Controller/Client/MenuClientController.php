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
    #[Route("/admin/client/{message?}", name: "menuClient", methods: ["GET"])]
    public function show(ClientRepository $clientRepository, EntityManagerInterface $manager, ?string $message): Response {
        if (!$this->isGranted(SymfonyRole::ASSOC)) {
            return $this->redirectToRoute('home');
        }

        $parameterManager = new ParameterManager($manager);
        $parameter = $parameterManager->getParameter();
        $clients = $clientRepository->findAll();

        return $this->render("client/MenuClient.html.twig", [
            "parameter" => $parameter,
            "clients" => $clients,
            "message" => $message
        ]);
    }

}
