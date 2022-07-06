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
     * @Route("/blog/edit/{!id}", name="edit_blog",methods={"GET", "POST"})
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
        $validationErrors = "";
        $postExistsError = "";

        if ($data->count() > 0) {
            $postType = $data->get('postType');

            $postmanager->setData($post, $postType, $data->get("postTitle"), $data->get('postDescription'), $data->get('imageLink'));
            $validationErrors = $validator->validate($post);

            if ($validationErrors->count() == 0) {
                $selectedPostName = $postRepository->find($id)->getTitle();
                $editedPostName = $post->getTitle();
                $selectedPostDescription = $postRepository->find($id)->getDescription();
                $editedPostDescription = $post->getDescription();

                if (strcmp($selectedPostName, $editedPostName) != 0 &&
                    strcmp($selectedPostDescription, $editedPostDescription) != 0 &&
                    $postRepository->findOneBy(['title' => $post->getTitle()]))
                {
                    $postExistsError = "Le post existe déjà.";
                }else{
                    $postmanager->persist($post);

                    return $this->redirectToRoute('blog', [
                        "message" => "Modification avec succès"
                    ]);
                }
            }
        }


        return $this->render('blog/EditModalBlog.html.twig', [
            'postTypes' => $postTypes,
            'validationErrors' => $validationErrors,
            'postExistsError' => $postExistsError,
            'post' => $post
        ]);
    }
}
