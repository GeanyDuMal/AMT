<?php

namespace App\Controller\Blog;

use App\Repository\PostRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MenuBlogController extends AbstractController
{
    /**
     * @Route("/blog/{message?}", name="menuBlog")
     */
    public function index(PostRepository $postRepository, string $message = null): Response
    {
        $blogs = $postRepository->findBy([], ["id" => "DESC"]);

        return $this->render('blog/MenuBlog.html.twig',[
          'blogs' => $blogs,
          "message" => $message
        ]);
    }
}
