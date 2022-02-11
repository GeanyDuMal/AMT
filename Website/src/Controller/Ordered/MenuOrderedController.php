<?php

namespace App\Controller\Ordered;

use App\Entity\Ordered;
use App\Manager\OrderedManager;
use App\Repository\OrderedRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MenuOrderedController extends AbstractController
{
    /**
     * @Route("/ordered/menu/{message}", name="orderedMenu")
     */
    public function menu(string $message = null ,EntityManagerInterface $manager, OrderedRepository $orderedRepository): Response
    {
        if (!$this->isGranted('ROLE_TRESORIER')){
            return $this->redirectToRoute('home');
        }

        $orderedManager = new OrderedManager($manager);
        $montantIdOrder = [];

        /*
         * Recuperer toute les commandes avec leurs clients et leurs types
         * Tout faire en une seule requetes, plus opti
         */
        $allOrder = $orderedRepository->findAllOrderAndClientAndClientType();
        //Créer un tableau avec en clé les id des commandes et en valeur le tarif total de la commande
        foreach ($allOrder as $order){
            $montantIdOrder = $montantIdOrder + [$order->getId() => $orderedManager->montantTotal($order)];
        }


        return $this->render('ordered/menu.html.twig', [
            "user" => $this->getUser(),
            "message" => $message,
            "orderedList" => $allOrder,
            "montantOrdered" => $montantIdOrder
        ]);
    }
}
