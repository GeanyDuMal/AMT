<?php

namespace App\Controller\Post;


use App\Manager\ParameterManager;
use App\Manager\PostManager;
use App\Repository\PostRepository;
use App\Utils\Enum\PostType;
use App\Utils\Enum\SymfonyRole;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class EditPostController extends AbstractController
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

    #[Route("/post/edit/{!id}", name: "editPost", methods: ["GET", "POST"])]
    public function index($id, Request $request): Response {
        $parameter = $this->parameterManager->getParameter();

        if (!$this->isGranted(SymfonyRole::ASSOC) || !$parameter->isPostActivated()) {
            return $this->redirectToRoute('home');
        }

        $data = $request->request;
        $post = $this->postRepository->find($id);
        $postTypes = PostType::getAll();
        $message = "";

        if ($data->count() > 0 && $post) {
            $this->postManager->setData($post,
                                  $data->get('postType'),
                                  $data->get("postTitle"),
                                  trim($data->get('postDescription')),
                                  $post->getCreationDate(),
                                  $post->getImageLink());

            if ($this->postManager->verifyPost($post)) {
                if ($data->get("pictureState") === "edit"){
                    $this->postManager->downloadPicture($post, $request->files->get("postPicture"));
                }

                $this->postManager->persist($post);

                return $this->redirectToRoute("menuPost", [
                    "message" => "Modification effectué avec succès"
                ]);
            } else {
                $message = "Votre saisie contient une erreur, merci de vérifier votre saisie";
            }
        }

        return $this->render("post/EditPost.html.twig", [
            "parameter" => $parameter,
            "postTypes" => $postTypes,
            "message" => $message,
            "post" => $post
        ]);
    }
}
