<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class POUETHomeController extends AbstractController
{
    /*
     * @Route("/pouethome", name="pouethome")
     */
    public function index(): Response
    {
        return $this->render('pouethome/index.html.twig', [
            'controller_name' => 'POUETHomeController',
        ]);
    }
}
