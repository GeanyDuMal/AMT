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
    private EntityManagerInterface $manager;
    private PostManager $postManager;
    private ParameterManager $parameterManager;
    private PostRepository $postRepository;

    public function __construct(EntityManagerInterface $manager, PostRepository $postRepository) {
        $this->manager = $manager;
        $this->postManager = new PostManager($this->manager);
        $this->parameterManager = new ParameterManager($this->manager);
        $this->postRepository = $postRepository;
    }

    #[Route("/post/create", name: "createPost", methods: ["GET", "POST"])]
    public function index(Request $request): Response {
        $parameter = $this->parameterManager->getParameter();

        if (!$this->isGranted(SymfonyRole::ASSOC) || !$parameter->isPostActivated()) {
            return $this->redirectToRoute('home');
        }

        $data = $request->request;
        $postTypes = PostType::getAll();
        $postExistsError = "";
        $post = new Post();
        $message = "";

        if ($data->count() > 0) {
            $this->postManager->setData($post,
                                  $data->get('postType'),
                                  $data->get("postTitle"),
                                  trim($data->get('postDescription')),
                                  new DateTime("now"));
            $this->postManager->downloadPicture($post, $request->files->get("postPicture"));

            if ($this->postManager->verifyPost($post)) {
                if ($this->postRepository->findBy(['title' => $post->getTitle()])) {
                    $postExistsError = "Le post existe déjà.";
                } else {
                    $this->postManager->persist($post);

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
