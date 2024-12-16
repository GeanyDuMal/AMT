<?php

namespace App\Tests\Unit\Entity;

use App\Entity\Price;
use App\Entity\Product;
use App\Utils\Enum\ClientTypeEnum;
use PHPUnit\Framework\TestCase;

class PriceTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->price = new Price();
        $this->product = new Product();

    }


    public function testGetClientType()
    {
        $value = ClientTypeEnum::ETUDIANT;

        $response = $this->price->setClientType($value);

        self::assertInstanceOf(Price::class, $response);
        self::assertContains($this->price->getClientType(), ClientTypeEnum::getAll());
        self::assertEquals($value, $this->price->getClientType());

    }

    public function testGetPrice()
    {
        $value = '0.6';

        $response = $this->price->setPrice($value);

        self::assertInstanceOf(Price::class, $response);
        self::assertEquals(floatval($value), floatval($this->price->getPrice()));

    }

    public function testGetProduct()
    {
        $value = 'Snickers';

        $responseP = $this->product->setName($value);
        $response = $this->price->setProduct($responseP);

        self::assertInstanceOf(Product::class, $responseP);
        self::assertInstanceOf(Price::class, $response);
        self::assertInstanceOf(Product::class, $this->price->getProduct());
        self::assertEquals($value, $this->price->getProduct()->getName());


    }
}
