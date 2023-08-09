<?php

namespace App\Controller\Post;

use App\Manager\ParameterManager;
use App\Repository\PostRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MenuPostController extends AbstractController
{
    /**
     * @Route("/post/{message?}", name="menuPost")
     */
    public function index(EntityManagerInterface $manager, PostRepository $postRepository, string $message = null): Response {
        $parameterManager = new ParameterManager($manager);
        $parameter = $parameterManager->getParameter();
        $posts = $postRepository->findBy([], ["creationDate" => "DESC"]);

        return $this->render('post/MenuPost.html.twig', [
            "parameter" => $parameter,
            "posts" => $posts,
            "message" => $message
        ]);
    }
}
