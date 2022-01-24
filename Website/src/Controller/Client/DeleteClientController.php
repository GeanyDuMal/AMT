<?php

namespace App\Controller\Client;

use App\Entity\Client;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class DeleteClientController extends AbstractController
{

    /**
     * @Route("/admin/client/delete/{id}", name="delete_client", methods={"GET", "DELETE"})
     */
    public function index($id, EntityManagerInterface $manager)
    {
        if (!$this->isGranted('ROLE_PRESIDENT')){
            return $this->redirectToRoute('home');
        }

        /*
         * when we delete a client
         * we delete it from association too
         * -> manipulated by a trigger called : deleteFromAssosIfMemberDeleted
         */
        $client = $manager->getRepository(Client::class)->find($id);
        $manager->remove($client);
        $manager->flush();
    }
}