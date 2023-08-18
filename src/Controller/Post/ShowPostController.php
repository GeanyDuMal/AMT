<?php

namespace App\Controller\Post;

use App\Manager\ParameterManager;
use App\Repository\PostRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ShowPostController extends AbstractController
{
    /**
     * @Route("/post/show/{!id}", name="showPost")
     */
    public function index(EntityManagerInterface $manager, int $id, PostRepository $postRepository): Response {
        $parameterManager = new ParameterManager($manager);
        $parameter = $parameterManager->getParameter();

        if (!$this->isGranted('ROLE_ASSOC') || !$parameter->isPostActivated()) {
            return $this->redirectToRoute('home');
        }

        $post = $postRepository->find($id);

        if ($post) {
            return $this->render("post/ShowPost.html.twig", [
                "parameter" => $parameter,
                "post" => $post,
            ]);
        } else {
            return $this->redirectToRoute("menuPost");
        }
    }
}
