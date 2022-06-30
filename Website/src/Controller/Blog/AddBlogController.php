<?php

namespace App\Controller\Blog;

use App\Entity\Post;
use App\Manager\PostManager;
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
     */
    public function index(EntityManagerInterface $manager, PostRepository $postRepository, Request $request,
                          PostTypeRepository $postTypeRepository, ValidatorInterface $validator): Response
    {
        if (!$this->isGranted('ROLE_ASSOC')){
            return $this->redirectToRoute('home');
        }

        $data = $request->request;
        $postTypes = $postTypeRepository->findAll();
        $validationErrors = "";
        $postExistsError = "";
        $post = new Post();
        $postmanager = new PostManager($manager);

        if($data->count() > 0){
            $type = $data->get('postType');
            $postType = $postTypeRepository->findOneBy(["name" => $type]);

            $postmanager->setData($post,$postType, $data->get("postTitle"), $data->get('postDescription'), $data->get('imageLink'));
            $validationErrors = $validator->validate($post);

            if($validationErrors->count() == 0){
                if($postRepository->findBy(['title' => $post->getTitle()])){
                    $postExistsError = "Le post existe déjà.";
                }else{
                    $postmanager->persist($post);

                    return $this->redirectToRoute('blog',[
                        "message" => "Ajout avec succès"
                    ]);
                }

            }
        }

        return $this->render('blog/AddModalBlog.html.twig', [
            'postTypes' => $postTypes,
            'validationErrors' => $validationErrors,
            'postExistsError' => $postExistsError,
            'post' => $post
        ]);
    }
}
