<?php

namespace App\Controller\Post;

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

class CreatePostController extends AbstractController
{
    /**
     * @Route("/post/create", name="createPost")
     */
    public function index(EntityManagerInterface $manager, PostRepository $postRepository, Request $request): Response {
        if (!$this->isGranted('ROLE_ASSOC')) {
            return $this->redirectToRoute('home');
        }

        $data = $request->request;
        $postTypes = PostType::getAll();
        $postExistsError = "";
        $post = new Post();
        $postmanager = new PostManager($manager);
        $message = "";

        if ($data->count() > 0) {

            $postmanager->setData($post, $data->get('postType'), $data->get("postTitle"), trim($data->get('postDescription')), new DateTime("now"));
            $postmanager->downloadPicture($data->get('imageLink'), $post);

            if ($postmanager->verifyPost($post)) {
                if ($postRepository->findBy(['title' => $post->getTitle()])) {
                    $postExistsError = "Le post existe déjà.";
                } else {
                    $postmanager->persist($post);

                    return $this->redirectToRoute("menuPost", [
                        "message" => "Ajout avec succès"
                    ]);
                }
            } else {
                $message = "Votre saisie contient une erreur, merci de vérifier votre saisie";
            }
        }

        return $this->render('post/CreatePost.html.twig', [
            'postTypes' => $postTypes,
            'message' => $message,
            'postExistsError' => $postExistsError,
            'post' => $post
        ]);
    }
}
