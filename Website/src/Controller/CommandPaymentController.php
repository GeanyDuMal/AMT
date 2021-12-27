<?php

namespace App\Controller;

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
        return $this->render('command_payment/index.html.twig', [
            'controller_name' => 'CommandPaymentController',
        ]);
    }
}
