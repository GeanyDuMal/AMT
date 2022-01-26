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

class AddBlogController extends AbstractController
{
    /**
     * @Route("/blog/add", name="add_blog")
     * @param EntityManagerInterface $manager
     * @param PostRepository $postRepository
     * @param Request $request
     * @param PostTypeRepository $postTypeRepository
     * @param ValidatorInterface $validator
     * @return Response
     */
    public function index(EntityManagerInterface $manager, PostRepository $postRepository, Request $request, PostTypeRepository $postTypeRepository,ValidatorInterface $validator): Response
    {
        if (!$this->isGranted('ROLE_ASSOC')){
            return $this->redirectToRoute('home');
        }

        $data = $request->request;
        $post = new Post();
        $postTypes = $postTypeRepository->findAll();
        $validationErrors = "";
        $postExistsError = "";

        if($data->count() > 0){
            $commonMethods = new CommonBlogMethods();
            $commonMethods->setData($post, $request, $postTypeRepository);
            $validationErrors = $validator->validate($post);
            if($validationErrors->count() == 0){
                if($postRepository->findBy(['title' => $post->getTitle()])){
                    $postExistsError = "Le post existe déjà.";
                }else{
                    $manager->persist($post);
                    $manager->flush();
                    return $this->redirectToRoute('blog',[
                        "message" => "Ajout avec succès"
                    ]);
                }

            }
        }

        return $this->render('blog/AddModalBlog.html.twig', [
            'postTypes'=>$postTypes,
            'validationErrors'=>$validationErrors,
            'postExistsError'=>$postExistsError,
            'post'=>$post
        ]);
    }
}
