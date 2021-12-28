<?php

namespace App\Controller\Command;

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
    public function index(EntityManagerInterface $manager, Request $request): Response
    {
        $commandeRepository = $manager->getRepository(Command::class);
        $orderManager = new OrderManager($manager);

        $allOrder = $commandeRepository->findBy([], ["orderedAt" => "DESC"]);
        $montantId = [];
        foreach ($allOrder as $order){
            $montantId = $montantId + [$order->getId() => $orderManager->montantTotal($order)];
        }

        return $this->render('command/menu.html.twig', [
            "orderList" => $allOrder,
            "montantOrder" => $montantId
        ]);
    }
}
