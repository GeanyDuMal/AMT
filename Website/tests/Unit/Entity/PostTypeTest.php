<?php

namespace App\Tests\Unit\Entity;

use App\Entity\PostType;
use PHPUnit\Framework\TestCase;

class PostTypeTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->postType = new PostType();
    }


    public function testGetName()
    {
        $value='Event';

        $response=$this->postType->setName($value);

        self::assertInstanceOf(PostType::class,$response);
        self::assertEquals($value,$this->postType->getName());

    }
}
