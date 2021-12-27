<?php

namespace App\Controller\Command;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CommandPaymentController extends AbstractController
{
    /**
     * @Route("/command/payment", name="command_payment")
     */
    public function index(): Response
    {
        $this->redirectToRoute("home");
    }
}
