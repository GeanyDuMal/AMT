<?php

namespace App\Controller\Post;

use App\Manager\ParameterManager;
use App\Manager\PostManager;
use App\Repository\PostRepository;
use App\Utils\Enum\SymfonyRole;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Annotation\Route;


class DeletePostController extends AbstractController
{
    private EntityManagerInterface $manager;
    private PostManager $postManager;
    private ParameterManager $parameterManager;
    private PostRepository $postRepository;

    public function __construct(EntityManagerInterface $manager, PostRepository $postRepository) {
        $this->manager = $manager;
        $this->postManager = new PostManager($this->manager);
        $this->parameterManager = new ParameterManager($this->manager);
        $this->postRepository = $postRepository;
    }

    #[Route("/post/delete/{!id}", name: "deletePost", methods: ["GET", "DELETE"])]
    public function index($id): RedirectResponse|JsonResponse
    {
        $parameter = $this->parameterManager->getParameter();

        if (!$this->isGranted(SymfonyRole::ASSOC) || !$parameter->isPostActivated()) {
            return $this->redirectToRoute('home');
        }

        $post = $this->postRepository->find($id);

        if ($post){
            $this->postManager->remove($post);
            return $this->redirectToRoute("menuPost");
        } else {
            return $this->redirectToRoute("home");
        }
    }
}