<?php

namespace App\Controller\Client;

use App\Repository\ClientRepository;
use App\Utils\Enum\SymfonyRole;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ShowClientController extends AbstractController
{
    /**
     * @Route("/admin/client/{message,}", name="client_list",methods={"GET", "POST"} )
     */
    public function show(ClientRepository $clientRepository, string $message = null): Response
    {
        if (!$this->isGranted(SymfonyRole::ROLE_PRESIDENT)) {
            return $this->redirectToRoute('home');
        }

        $clients = $clientRepository->findAll();

        return $this->render('client/index.html.twig', [
            'clients' => $clients,
            'message' => $message
        ]);
    }

}
