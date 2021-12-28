<?php

namespace App\Controller\Command;

use App\Entity\Command;
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

        $allOrder = $commandeRepository->findBy([], ["orderedAt" => "DESC"]);
        return $this->render('command/menu.html.twig', [
            "orderList" => $allOrder
        ]);
    }
}
