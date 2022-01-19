<?php

namespace App\Tests\Unit;

use App\Entity\Post;
use App\Entity\PostType;
use PHPUnit\Framework\TestCase;

class PostTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->post = new Post();
        $this->postType= new PostType();
    }


    public function testGetTitle()
    {
        $value='post title';

        $response=$this->post->setTitle($value);

        self::assertInstanceOf(Post::class,$response);
        self::assertEquals($value,$this->post->getTitle());

    }

    public function testGetDescription()
    {
        $value='this is a long text to put in the description';

        $response=$this->post->setDescription($value);

        self::assertInstanceOf(Post::class,$response);
        self::assertEquals($value,$this->post->getDescription());

    }

    public function testGetImageLink()
    {
        $value='https://thisisalink.notfound/image';

        $response=$this->post->setImageLink($value);

        self::assertInstanceOf(Post::class,$response);
        self::assertEquals($value,$this->post->getImageLink());

    }

    public function testGetPostType()
    {
        $value='event';

        $responsePT=$this->postType->setName($value);
        $response=$this->post->setPostType($responsePT);

        self::assertInstanceOf(PostType::class,$responsePT);
        self::assertInstanceOf(Post::class,$response);
        self::assertInstanceOf(PostType::class,$this->post->getPostType());
        self::assertEquals($value,$this->post->getPostType()->getName());
    }
}
