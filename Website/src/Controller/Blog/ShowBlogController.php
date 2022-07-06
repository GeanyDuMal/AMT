<?php

namespace App\Controller\Blog;

use App\Repository\PostRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ShowBlogController extends AbstractController
{
    /**
     * @Route("/blog/{message?}", name="blog")
     */
    public function index(EntityManagerInterface $manager, PostRepository $postRepository,
                          string $message = null): Response
    {
        $blogs = $postRepository->findBy([], ["id" => "DESC"]);

        return $this->render('blog/index.html.twig',[
          'blogs' => $blogs,
          "message" => $message
        ]);
    }

    /**
     * @Route("/blog/show/{!id}", name="selected_blog")
     */
    public function showBlog($id, PostRepository $postRepository, EntityManagerInterface $manager): Response
    {
        $blog = $postRepository->find($id);

        if ($blog){
            return $this->render('blog/ShowOneBlog.html.twig',[
                'blog' => $blog,
            ]);
        }else{
            return $this->redirectToRoute('blog');
        }
    }
}
