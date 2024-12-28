<?php

namespace App\Controller\Post;

use App\Manager\ParameterManager;
use App\Repository\PostRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ShowPostController extends AbstractController
{
    private EntityManagerInterface $manager;
    private ParameterManager $parameterManager;
    private PostRepository $postRepository;

    public function __construct(EntityManagerInterface $manager, PostRepository $postRepository) {
        $this->manager = $manager;
        $this->parameterManager = new ParameterManager($this->manager);
        $this->postRepository = $postRepository;
    }
    #[Route("/post/show/{!id}", name: "showPost", methods: ["GET"])]
    public function index(int $id): Response {
        $parameter = $this->parameterManager->getParameter();

        if (!$parameter->isPostActivated()) {
            return $this->redirectToRoute('home');
        }

        $post = $this->postRepository->find($id);

        if ($post) {
            return $this->render("post/ShowPost.html.twig", [
                "parameter" => $parameter,
                "post" => $post,
            ]);
        } else {
            return $this->redirectToRoute("menuPost");
        }
    }
}
