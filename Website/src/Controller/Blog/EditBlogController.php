<?php

namespace App\Controller\Blog;

use App\Entity\Post;
use App\Repository\PostRepository;

use App\Repository\PostTypeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class EditBlogController extends AbstractController
{
    /**
     * @Route("/edit/blog/edit/{id}", name="edit_blog",methods={"GET", "POST"})
     */
    public function index($id, PostRepository $postRepository, ValidatorInterface $validator, Request $request, EntityManagerInterface $manager, PostTypeRepository $postTypeRepository): Response
    {
        if(!$this->isGranted('ROLE_ASSOC')){
            return $this->redirectToRoute('home');
        }

        $data=$request->request;
        $post=$postRepository->find($id);
        $postType=$postTypeRepository->findAll();


        return $this->render('edit_blog/EditModalBlog.html.twig', [
            'controller_name' => 'EditBlogController',
        ]);
    }
}
