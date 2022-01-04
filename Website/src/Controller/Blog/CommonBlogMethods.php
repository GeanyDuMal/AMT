<?php

namespace App\Controller\Blog;


use App\Entity\Post;
use App\Repository\PostTypeRepository;
use Symfony\Component\HttpFoundation\Request;

class CommonBlogMethods
{
    /**
     * @param Post $post
     * @param Request $request
     * @param PostTypeRepository $postTypeRepository
     * @return void
     */
    public function setData(Post $post, Request $request, PostTypeRepository $postTypeRepository){
        $data = $request->request;
        $postType=$data->get('postType');
        $type=$postTypeRepository->findOneBy(["name"=>$postType]);
        $postTitle=$data->get("postTitle");
        $postDescription=$data->get('postDescription');
        $imageLink=$data->get('imageLink');

        $post->setTitle($postTitle);
        $post->setDescription($postDescription);
        $post->setImageLink($imageLink);
        $post->setPostType($type);
    }

}