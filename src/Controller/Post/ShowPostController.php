<?php

namespace App\Controller\Post;

use App\Repository\PostRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ShowPostController extends AbstractController
{
    /**
     * @Route("/post/show/{!id}", name="showPost")
     */
    public function index($id, PostRepository $postRepository): Response {
        $post = $postRepository->find($id);

        if ($post) {
            return $this->render('post/ShowPost.html.twig', [
                "post" => $post,
            ]);
        } else {
            return $this->redirectToRoute("menuPost");
        }
    }
}
