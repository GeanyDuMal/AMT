<?php

namespace App\Controller\Blog;

use App\Entity\Post;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ShowBlogController extends AbstractController
{
    /**
     * @Route("/blog/{message}", name="blog")
     */
    public function index(string $message = null , EntityManagerInterface $manager): Response
    {
        //findBy plutot que findAll car on peut trier et recuperer le dernier post en premier
        $blogs=$manager->getRepository(Post::class)->findBy([], ["id" => "DESC"]);

        return $this->render('blog/index.html.twig',[
          'blogs' => $blogs,
          "message" => $message
        ]);
    }

    /**
     * @Route("/blog/{id}", name="selected_blog")
     */
    public function showBlog($id, EntityManagerInterface $manager): Response
    {
        $blog=$manager->getRepository(Post::class)->find($id);

        return $this->render('blog/ShowOneBlog.html.twig',[
            'blog' => $blog,
        ]);
    }
}
