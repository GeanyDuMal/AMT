<?php

namespace App\Controller\Command;

use App\Entity\Command;
use App\Entity\Purchase;
use App\Manager\OrderManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MenuOrderController extends AbstractController
{
    /**
     * @Route("/command/menu{message}", name="orderMenu")
     */
    public function menu(string $message = null ,EntityManagerInterface $manager): Response
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
            "user" => $this->getUser(),
            "message" => $message,
            "orderList" => $allOrder,
            "montantOrder" => $montantIdOrder
        ]);
    }


    /**
     * @Route("/command/menu/delete/{id}", name="orderDelete", methods={"GET", "DELETE"})
     */
    public function delete($id, EntityManagerInterface $manager){
        if (!$this->isGranted('ROLE_TRESORIER')){
            return $this->redirectToRoute('home');
        }
        /**
         * Delete an order will delete all the purchase linked
         */
        $orderManager = $manager->getRepository(Command::class);
        $purchaseManager = $manager->getRepository(Purchase::class);

        $order = $orderManager->find($id);
        $purchaseList = $purchaseManager->findBy(["command" => $order]);

        foreach ($purchaseList as $purchase){
            $manager->remove($purchase);
        }
        $manager->flush();

        $manager->remove($order);
        $manager->flush();
        return new JsonResponse(true);
    }
}
