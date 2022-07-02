<?php

namespace App\Controller\Blog;

use App\Repository\PostRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;


class DeleteBlogController extends AbstractController
{
    /**
     * @Route("/blog/delete/{!id}", name="delete_blog", methods={"GET", "DELETE"})
     */
    public function index($id, EntityManagerInterface $manager, PostRepository $postRepository): JsonResponse
{
        if(!$this->isGranted('ROLE_ASSOC')){
            return new JsonResponse(false);
        }

        $post = $postRepository->find($id);

        $manager->remove($post);
        $manager->flush();
        return new JsonResponse(true);
    }

}