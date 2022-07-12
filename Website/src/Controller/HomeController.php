<?php

namespace App\Controller;

use App\Repository\PostRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    /**
     * @Route("/{message?}", name="home")
     */
    public function index(?string $message): Response
    {
        return $this->render('home/index.html.twig', [
            "message" => $message
        ]);
    }
}
