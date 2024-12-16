<?php

namespace App\Controller\UserGuide;

use App\Manager\ParameterManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserGuideController extends AbstractController
{
    private EntityManagerInterface $manager;
    private ParameterManager $parameterManager;

    public function __construct(EntityManagerInterface $manager) {
        $this->manager = $manager;
        $this->parameterManager = new ParameterManager($this->manager);
    }

    /**
     * @Route("/userGuide", name="userGuide")
     */
    #[Route("/userGuide", name: "userGuide", methods: ["GET"])]
    public function index(): Response {
        $parameter = $this->parameterManager->getParameter();

        return $this->render("userGuide/UserGuide.html.twig", [
            "parameter" => $parameter
        ]);
    }
}
