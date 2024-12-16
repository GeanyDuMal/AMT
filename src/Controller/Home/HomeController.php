<?php

namespace App\Controller\Home;

use App\Manager\ParameterManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    private EntityManagerInterface $manager;
    private ParameterManager $parameterManager;

    public function __construct(EntityManagerInterface $manager) {
        $this->manager = $manager;
        $this->parameterManager = new ParameterManager($this->manager);
    }

    #[Route("/", name: "home", methods: ["GET"])]
    public function index(): Response {
        $parameter = $this->parameterManager->getParameter(true);

        return $this->render("home/Home.html.twig", [
            "parameter" => $parameter
        ]);
    }
}
