<?php

namespace App\Controller\Command;

use App\Entity\Command;
use App\Manager\OrderManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MenuOrderController extends AbstractController
{
    /**
     * @Route("/order/menu{message}", name="orderMenu")
     */
    public function menu(string $message = null ,EntityManagerInterface $manager): Response
    {
        if (!$this->isGranted('ROLE_ASSOC')){
            return $this->redirectToRoute('home');
        }

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


        return $this->render('order/menu.html.twig', [
            "user" => $this->getUser(),
            "message" => $message,
            "orderList" => $allOrder,
            "montantOrder" => $montantIdOrder
        ]);
    }
}
