<?php

namespace App\Controller\Client;

use App\Repository\ClientRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Client;
use Doctrine\ORM\EntityManagerInterface;

class ShowClientController extends AbstractController
{
    /**
     * @Route("/admin/client/{message}", name="client_list",methods={"GET", "POST"} )
     */
    public function show(string $message = null, ClientRepository $clientRepository): Response
    {
        if (!$this->isGranted('ROLE_PRESIDENT')){
            return $this->redirectToRoute('home');
        }

        $clients = $clientRepository->findAll();

        return $this->render('client/index.html.twig', [
            'clients' => $clients,
            'message'=>$message
        ]);
    }

}
