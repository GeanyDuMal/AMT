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
    /**
     * @Route("/post/edit/{!id}", name="editPost",methods={"GET", "POST"})
     */
    public function index($id, PostRepository $postRepository, Request $request,
        EntityManagerInterface $manager): Response {
        $parameterManager = new ParameterManager($manager);
        $parameter = $parameterManager->getParameter();

        if (!$this->isGranted(SymfonyRole::ASSOC) || !$parameter->isPostActivated()) {
            return $this->redirectToRoute('home');
        }

        $data = $request->request;
        $post = $postRepository->find($id);
        $postTypes = PostType::getAll();
        $postmanager = new PostManager($manager);
        $message = "";

        if ($data->count() > 0 && $post) {
            $postmanager->setData($post,
                                  $data->get('postType'),
                                  $data->get("postTitle"),
                                  trim($data->get('postDescription')),
                                  $post->getCreationDate(),
                                  $post->getImageLink());

            if ($postmanager->verifyPost($post)) {
                if ($data->get("pictureState") === "edit"){
                    $postmanager->downloadPicture($post, $request->files->get("postPicture"));
                }

                $postmanager->persist($post);

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
