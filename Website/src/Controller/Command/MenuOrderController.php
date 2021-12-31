<?php

namespace App\Controller\Command;

use App\Entity\Client;
use App\Entity\ClientType;
use App\Entity\Command;
use App\Manager\OrderManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MenuOrderController extends AbstractController
{
    /**
     * @Route("/command/menu", name="orderMenu")
     */
    public function menu(EntityManagerInterface $manager): Response
    {
        $commandeRepository = $manager->getRepository(Command::class);
        $orderManager = new OrderManager($manager);
        $montantIdOrder = [];

        /**
         * Recuperer toute les commandes avec leurs clients et leurs types
         * Tout faire en une seule requetes, plus opti
         */
        $allOrder = $commandeRepository->findAllOrderAndClientAndClientType();
        foreach ($allOrder as $order){
            $montantIdOrder = $montantIdOrder + [$order->getId() => $orderManager->montantTotal($order)];
        }

        return $this->render('command/menu.html.twig', [
            "orderList" => $allOrder,
            "montantOrder" => $montantIdOrder
        ]);
    }

    /**
     * @Route("/command/menu{message}", name="orderMenuMessage")
     */
    public function menuWithMessage($message ,EntityManagerInterface $manager): Response
    {
        $commandeRepository = $manager->getRepository(Command::class);
        $orderManager = new OrderManager($manager);
        $montantIdOrder = [];

        /**
         * Recuperer toute les commandes avec leurs clients et leurs types
         * Tout faire en une seule requetes, plus opti
         */
        $allOrder = $commandeRepository->findAllOrderAndClientAndClientType();
        foreach ($allOrder as $order){
            $montantIdOrder = $montantIdOrder + [$order->getId() => $orderManager->montantTotal($order)];
        }


        return $this->render('command/menu.html.twig', [
            "message" => $message,
            "orderList" => $allOrder,
            "montantOrder" => $montantIdOrder
        ]);
    }
}
