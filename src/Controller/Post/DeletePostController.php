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

    #[Route("/post/delete/{!id}", name: "deletePost", methods: ["GET", "DELETE"])]
    public function index($id, EntityManagerInterface $manager, PostRepository $postRepository): RedirectResponse|JsonResponse
    {
        $parameterManager = new ParameterManager($manager);
        $parameter = $parameterManager->getParameter();

        if (!$this->isGranted(SymfonyRole::ASSOC) || !$parameter->isPostActivated()) {
            return $this->redirectToRoute('home');
        }

        $postManager = new PostManager($manager);
        $post = $postRepository->find($id);

        if ($post){
            $postManager->remove($post);
            return $this->redirectToRoute("menuPost");
        } else {
            return $this->redirectToRoute("home");
        }
    }
}