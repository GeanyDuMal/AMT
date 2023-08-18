<?php

namespace App\Controller;

use App\Manager\ParameterManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function index(EntityManagerInterface $manager): Response
    {
        $parameterManager = new ParameterManager($manager);
        $parameter = $parameterManager->getParameter();

        return $this->render("home/Home.html.twig", [
            "parameter" => $parameter
        ]);
    }
}
