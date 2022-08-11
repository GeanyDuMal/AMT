<?php

namespace App\Controller\Blog;

use App\Entity\Post;
use App\Manager\PostManager;
use App\Repository\PostRepository;
use App\Utils\Enum\PostType;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class CreateBlogController extends AbstractController
{
    /**
     * @Route("/blog/create", name="createBlog")
     */
    public function index(EntityManagerInterface $manager, PostRepository $postRepository, Request $request): Response
    {
        if (!$this->isGranted('ROLE_ASSOC')){
            return $this->redirectToRoute('home');
        }

        $data = $request->request;
        $postTypes = PostType::getAll();
        $postExistsError = "";
        $post = new Post();
        $postmanager = new PostManager($manager);
        $message = "";

        if($data->count() > 0){
            $emplacementImage = $postmanager->downloadPicture($data->get('imageLink'));

            $postmanager->setData($post, $data->get('postType'), $data->get("postTitle"), $data->get('postDescription'), $emplacementImage, new DateTime("now"));

            if($postmanager->verifyPost($post)){
                if($postRepository->findBy(['title' => $post->getTitle()])){
                    $postExistsError = "Le post existe déjà.";
                }else{
                    $postmanager->persist($post);

                    return $this->redirectToRoute('menuBlog',[
                        "message" => "Ajout avec succès"
                    ]);
                }
            } else {
                $message = "Votre saisie contient une erreur, merci de vérifier votre saisie";
            }
        }

        return $this->render('blog/CreateBlog.html.twig', [
            'postTypes' => $postTypes,
            'message' => $message,
            'postExistsError' => $postExistsError,
            'post' => $post
        ]);
    }
}
