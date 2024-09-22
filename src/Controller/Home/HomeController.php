<?php

namespace App\Controller\Home;

use App\Manager\ParameterManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{

    #[Route("/", name: "home", methods: ["GET"])]
    public function index(EntityManagerInterface $manager): Response
    {
        $parameterManager = new ParameterManager($manager);
        $parameter = $parameterManager->getParameter(true);

        return $this->render("home/Home.html.twig", [
            "parameter" => $parameter
        ]);
    }
}
