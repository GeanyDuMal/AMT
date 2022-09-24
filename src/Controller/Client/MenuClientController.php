<?php

namespace App\Controller\Client;

use App\Repository\ClientRepository;
use App\Utils\Enum\SymfonyRole;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MenuClientController extends AbstractController
{
    /**
     * @Route("/admin/client/{message?}", name="menuClient",methods={"GET", "POST"} )
     */
    public function show(ClientRepository $clientRepository, ?string $message): Response
    {
        if (!$this->isGranted(SymfonyRole::SECRETAIRE)) {
            return $this->redirectToRoute('home');
        }

        $clients = $clientRepository->findAll();

        return $this->render('client/MenuClient.html.twig', [
            'clients' => $clients,
            'message' => $message
        ]);
    }

}
