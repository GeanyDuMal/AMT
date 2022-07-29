<?php

namespace App\Controller\Blog;

use App\Manager\PostManager;
use App\Repository\PostRepository;
use App\Utils\Enum\SymfonyRole;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Annotation\Route;


class DeleteBlogController extends AbstractController
{
    /**
     * @Route("/blog/delete/{!id}", name="deleteBlog", methods={"GET", "DELETE"})
     */
    public function index($id, EntityManagerInterface $manager, PostRepository $postRepository): RedirectResponse|JsonResponse
    {
        if(!$this->isGranted(SymfonyRole::ASSOC)){
            return $this->redirectToRoute("home");
        }

        $postManager = new PostManager($manager);
        $post = $postRepository->find($id);

        if ($post){
            $postManager->remove($post);
            return new JsonResponse(true);
        } else {
            return $this->redirectToRoute("home");
        }
    }
}