<?php

namespace App\Controller\Post;

use App\Manager\ParameterManager;
use App\Manager\PostManager;
use App\Repository\PostRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MenuPostController extends AbstractController {

    private EntityManagerInterface $manager;
    private ParameterManager $parameterManager;
    private PostRepository $postRepository;

    public function __construct(EntityManagerInterface $manager, PostRepository $postRepository) {
        $this->manager = $manager;
        $this->parameterManager = new ParameterManager($this->manager);
        $this->postRepository = $postRepository;
    }

    #[Route("/post/{message?}", name: "menuPost", methods: ["GET"])]
    public function index(string $message = null): Response {
        $parameter = $this->parameterManager->getParameter();

        if (!$parameter->isPostActivated()) {
            return $this->redirectToRoute('home');
        }

        $posts = $this->postRepository->findBy([], ["creationDate" => "DESC"]);

        return $this->render('post/MenuPost.html.twig', [
            "parameter" => $parameter,
            "posts" => $posts,
            "message" => $message
        ]);
    }
}
