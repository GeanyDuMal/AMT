<?php

namespace App\Controller\UserGuide;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserGuideController extends AbstractController
{
    /**
     * @Route("/userGuide", name="userGuide")
     */
    public function index(): Response
    {
        return $this->render("userGuide/UserGuide.html.twig");
    }
}
