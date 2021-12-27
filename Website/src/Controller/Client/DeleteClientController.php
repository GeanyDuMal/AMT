<?php

namespace App\Controller\Client;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DeleteClientController extends AbstractController
{

    /**
     * @Route("/admin/client/delete/{id}", name="delete_client", methods={"GET", "DELETE"})
     */
    public function deleteClientAction(Request $request,$id, EntityManagerInterface $manager): Response
    {
        if (!$this->isGranted('ROLE_PRESIDENT')){
            return $this->redirectToRoute('home');
        }
        $client = $manager->getRepository('App:Client')->find($id);
        $manager->remove($client);
        $manager->flush();
        $this->addFlash('message', 'Client supprimer avec succée');
        return $this->redirectToRoute('client_list');
    }
}