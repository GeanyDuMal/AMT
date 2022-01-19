<?php

namespace App\Tests\Unit\Entity;

use App\Entity\ProductType;
use PHPUnit\Framework\TestCase;

class ProductTypeTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->productType=new ProductType();
    }


    public function testGetName()
    {
        $value='Snack';

        $response=$this->productType->setName($value);

        self::assertInstanceOf(ProductType::class,$response);
        self::assertEquals($value,$this->productType->getName());

    }
}
