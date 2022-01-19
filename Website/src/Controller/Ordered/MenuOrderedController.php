<?php

namespace App\Controller\Ordered;

use App\Entity\Ordered;
use App\Manager\OrderedManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MenuOrderedController extends AbstractController
{
    /**
     * @Route("/ordered/menu/{message}", name="orderedMenu")
     */
    public function menu(string $message = null ,EntityManagerInterface $manager): Response
    {
        if (!$this->isGranted('ROLE_ASSOC')){
            return $this->redirectToRoute('home');
        }

        $commandeRepository = $manager->getRepository(Ordered::class);
        $orderManager = new OrderedManager($manager);
        $montantIdOrder = [];

        /**
         * Recuperer toute les commandes avec leurs clients et leurs types
         * Tout faire en une seule requetes, plus opti
         */
        $allOrder = $commandeRepository->findAllOrderAndClientAndClientType();
        foreach ($allOrder as $order){
            $montantIdOrder = $montantIdOrder + [$order->getId() => $orderManager->montantTotal($order)];
        }


        return $this->render('ordered/menu.html.twig', [
            "user" => $this->getUser(),
            "message" => $message,
            "orderedList" => $allOrder,
            "montantOrdered" => $montantIdOrder
        ]);
    }
}
