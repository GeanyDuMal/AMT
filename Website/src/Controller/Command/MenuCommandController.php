<?php

namespace App\Controller\Command;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MenuCommandController extends AbstractController
{
    /**
     * @Route("/command/menu", name="command_menu")
     */
    public function index(): Response
    {
        return $this->render('command/menu.html.twig', [
        ]);
    }
}
