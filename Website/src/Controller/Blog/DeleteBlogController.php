<?php

namespace App\Controller\Blog;

use App\Repository\PostRepository;
use App\Utils\Enum\SymfonyRole;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;


class DeleteBlogController extends AbstractController
{
    /**
     * @Route("/blog/delete/{!id}", name="deleteBlog", methods={"GET", "DELETE"})
     */
    public function index($id, EntityManagerInterface $manager, PostRepository $postRepository): JsonResponse
{
        if(!$this->isGranted(SymfonyRole::ASSOC)){
            return new JsonResponse(false);
        }

        $post = $postRepository->find($id);

        $manager->remove($post);
        $manager->flush();
        return new JsonResponse(true);
    }

}