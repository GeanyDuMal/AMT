<?php

namespace App\Controller\Command;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MenuOrderController extends AbstractController
{
    /**
     * @Route("/command/menu", name="orderMenu")
     */
    public function index(): Response
    {
        return $this->render('command/menu.html.twig', [
        ]);
    }
}
