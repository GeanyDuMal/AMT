<?php

namespace App\Controller\Client;

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
    public function show(string $message = null, EntityManagerInterface $manager): Response
    {
        if (!$this->isGranted('ROLE_PRESIDENT')){
            return $this->redirectToRoute('home');
        }
        $clients = $manager->getRepository(Client::class)->findAll();
        return $this->render('client/index.html.twig', [
            'clients' => $clients,
            'message'=>$message
        ]);
    }

}
