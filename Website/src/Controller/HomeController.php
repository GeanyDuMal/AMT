<?php

namespace App\Controller;

use App\Entity\Post;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    /**
     * @Route("/home", name="home")
     */
    public function index(EntityManagerInterface $manager): Response
    {
        $blogs=$manager->getRepository(Post::class)->findBy(array(),array('id'=>'DESC'),4,0);
        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
            'blogs' => $blogs
        ]);
    }
}
