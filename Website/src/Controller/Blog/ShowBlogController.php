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
     * @Route("/blog{message}", name="blog")
     */
    public function index(string $message = null , EntityManagerInterface $manager): Response
    {

        $blogs=$manager->getRepository(Post::class)->findAll();
        return $this->render('blog/index.html.twig',['blogs'=>$blogs,"message"=>$message]);
    }
}
