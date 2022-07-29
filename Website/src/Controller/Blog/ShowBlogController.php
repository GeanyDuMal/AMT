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
     * @Route("/blog/show/{!id}", name="showBlog")
     */
    public function showBlog($id, PostRepository $postRepository): Response
    {
        $blog = $postRepository->find($id);

        if ($blog){
            return $this->render('blog/ShowBlog.html.twig',[
                'blog' => $blog,
            ]);
        }else{
            return $this->redirectToRoute('menuBlog');
        }
    }
}
