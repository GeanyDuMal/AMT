<?php

namespace App\Controller\Post;

use App\Entity\Post;
use App\Manager\ParameterManager;
use App\Manager\PostManager;
use App\Repository\PostRepository;
use App\Utils\Enum\PostType;
use App\Utils\Enum\SymfonyRole;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CreatePostController extends AbstractController
{

    #[Route("/post/create", name: "createPost", methods: ["GET", "POST"])]
    public function index(EntityManagerInterface $manager, PostRepository $postRepository, Request $request): Response {
        $parameterManager = new ParameterManager($manager);
        $parameter = $parameterManager->getParameter();

        if (!$this->isGranted(SymfonyRole::ASSOC) || !$parameter->isPostActivated()) {
            return $this->redirectToRoute('home');
        }

        $data = $request->request;
        $postTypes = PostType::getAll();
        $postExistsError = "";
        $post = new Post();
        $postmanager = new PostManager($manager);
        $message = "";

        if ($data->count() > 0) {
            $postmanager->setData($post,
                                  $data->get('postType'),
                                  $data->get("postTitle"),
                                  trim($data->get('postDescription')),
                                  new DateTime("now"));
            $postmanager->downloadPicture($post, $request->files->get("postPicture"));

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
            "parameter" => $parameter,
            "postTypes" => $postTypes,
            "message" => $message,
            "postExistsError" => $postExistsError,
            "post" => $post
        ]);
    }
}
