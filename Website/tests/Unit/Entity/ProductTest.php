<?php

namespace App\Tests\Unit\Entity;

use App\Entity\Product;
use App\Entity\ProductType;
use PHPUnit\Framework\TestCase;

class ProductTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->product=new Product();
        $this->productType=new ProductType();
    }


    public function testGetQuantityStock()
    {
        $value=50;

        $response=$this->product->setQuantityStock($value);

        self::assertInstanceOf(Product::class,$response);
        self::assertEquals($value,$this->product->getQuantityStock());

    }

    public function testGetImageLink()
    {
        $value='https://thisisalink.notfound/image';

        $response=$this->product->setImageLink($value);

        self::assertInstanceOf(Product::class,$response);
        self::assertEquals($value,$this->product->getImageLink());

    }

    public function testGetProductType()
    {
        $value='Snack';

        $responsePT=$this->productType->setName($value);
        $response=$this->product->setProductType($responsePT);

        self::assertInstanceOf(Product::class,$response);
        self::assertInstanceOf(ProductType::class,$responsePT);
        self::assertInstanceOf(ProductType::class,$this->product->getProductType());
        self::assertEquals($value,$this->product->getProductType()->getName());

    }

    public function testGetName()
    {
        $value='Snickers';

        $response=$this->product->setName($value);

        self::assertInstanceOf(Product::class,$response);
        self::assertEquals($value,$this->product->getName());

    }
}
