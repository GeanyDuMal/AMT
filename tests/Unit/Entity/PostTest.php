<?php

namespace App\Tests\Unit\Entity;

use App\Entity\Post;
use App\Utils\Enum\PostType;
use PHPUnit\Framework\TestCase;

class PostTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->post = new Post();
    }


    public function testGetTitle()
    {
        $value = 'post title';

        $response = $this->post->setTitle($value);

        self::assertInstanceOf(Post::class, $response);
        self::assertEquals($value, $this->post->getTitle());

    }

    public function testGetDescription()
    {
        $value = 'this is a long text to put in the description';

        $response = $this->post->setDescription($value);

        self::assertInstanceOf(Post::class, $response);
        self::assertEquals($value, $this->post->getDescription());

    }

    public function testGetImageLink()
    {
        $value = 'https://thisisalink.notfound/image';

        $response = $this->post->setImageLink($value);

        self::assertInstanceOf(Post::class, $response);
        self::assertEquals($value, $this->post->getImageLink());

    }

    public function testGetPostType()
    {
        $value = PostType::EVENT;

        $response = $this->post->setPostType($value);

        self::assertInstanceOf(Post::class, $response);
        self::assertEquals($value, $this->post->getPostType());
        self::assertContains($this->post->getPostType(), PostType::getAll());
    }
}
