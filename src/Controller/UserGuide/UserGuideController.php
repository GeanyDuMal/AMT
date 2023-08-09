<?php

namespace App\Controller\UserGuide;

use App\Manager\ParameterManager;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserGuideController extends AbstractController
{
    /**
     * @Route("/userGuide", name="userGuide")
     */
    public function index(EntityManager $manager): Response {
        $parameterManager = new ParameterManager($manager);
        $parameter = $parameterManager->getParameter();

        return $this->render("userGuide/UserGuide.html.twig");
    }
}
