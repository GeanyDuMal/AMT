<?php

namespace App\Tests\Unit\Entity;

use App\Entity\Product;
use App\Utils\Enum\ProductType;
use PHPUnit\Framework\TestCase;

class ProductTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->product = new Product();
    }


    public function testGetQuantityStock()
    {
        $value = 50;

        $response = $this->product->setQuantityStock($value);

        self::assertInstanceOf(Product::class, $response);
        self::assertEquals($value, $this->product->getQuantityStock());

    }

    public function testGetImageLink()
    {
        $value = 'https://thisisalink.notfound/image';

        $response = $this->product->setImageLink($value);

        self::assertInstanceOf(Product::class, $response);
        self::assertEquals($value, $this->product->getImageLink());

    }

    public function testGetProductType()
    {
        $value = ProductType::SNACK;

        $response = $this->product->setProductType($value);

        self::assertInstanceOf(Product::class, $response);
        self::assertContains($this->product->getProductType(), ProductType::getAll());
        self::assertEquals($value, $this->product->getProductType());

    }

    public function testGetName()
    {
        $value = 'Snickers';

        $response = $this->product->setName($value);

        self::assertInstanceOf(Product::class, $response);
        self::assertEquals($value, $this->product->getName());

    }
}
