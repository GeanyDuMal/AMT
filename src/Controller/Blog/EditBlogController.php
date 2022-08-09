<?php

namespace App\Controller\Blog;


use App\Manager\PostManager;
use App\Repository\PostRepository;
use App\Utils\Enum\PostType;
use App\Utils\Enum\SymfonyRole;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class EditBlogController extends AbstractController
{
    /**
     * @Route("/blog/edit/{!id}", name="editBlog",methods={"GET", "POST"})
     */
    public function index($id, PostRepository $postRepository, ValidatorInterface $validator, Request $request,
                          EntityManagerInterface $manager): Response
    {
        if (!$this->isGranted(SymfonyRole::ASSOC)) {
            return $this->redirectToRoute('home');
        }

        $data = $request->request;
        $post = $postRepository->find($id);
        $postTypes = PostType::getAll();
        $postmanager = new PostManager($manager);
        $message = "";

        if ($data->count() > 0 && $post) {

            $postmanager->setData($post, $data->get('postType'), $data->get("postTitle"), $data->get('postDescription'), $data->get('imageLink'), $post->getCreationDate());

            if ($postmanager->verifyPost($post)) {
                $originalPost = $postRepository->find($id);

                if ($post->equals($originalPost))
                {
                    $message = "Le post existe déjà.";
                }else{
                    $postmanager->persist($post);

                    return $this->redirectToRoute('menuBlog', [
                        "message" => "Modification effectué avec succès"
                    ]);
                }
            } else {
                $message = "Votre saisie contient une erreur, merci de vérifier votre saisie";
            }
        }


        return $this->render('blog/EditBlog.html.twig', [
            'postTypes' => $postTypes,
            'message' => $message,
            'post' => $post
        ]);
    }
}
