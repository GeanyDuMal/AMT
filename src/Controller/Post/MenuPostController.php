<?php

namespace App\Controller\Post;

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
    public function index(PostRepository $postRepository, string $message = null): Response
    {
        $posts = $postRepository->findBy([], ["creationDate" => "DESC"]);

        return $this->render('post/MenuPost.html.twig',[
          "posts" => $posts,
          "message" => $message
        ]);
    }
}
