<?php

namespace App\Controller\Client;

use App\Repository\AssociationRepository;
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
    public function index(Request $request,$id, EntityManagerInterface $manager,AssociationRepository $associationRepository)
    {
        if (!$this->isGranted('ROLE_PRESIDENT')){
            return $this->redirectToRoute('home');
        }
        $query=$manager->createQuery(
            "DELETE App:Association a where a.member=:id")
            ->setParameter("id",$id);
        $query->execute();
        $client = $manager->getRepository('App:Client')->find($id);
        $manager->remove($client);
        $manager->flush();

    }
}