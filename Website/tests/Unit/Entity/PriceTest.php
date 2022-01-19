<?php

namespace App\Tests\Unit\Entity\Entity;


use App\Entity\ClientType;
use App\Entity\Price;
use App\Entity\Product;
use PHPUnit\Framework\TestCase;

class PriceTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->clientType=new ClientType();
        $this->price=new Price();
        $this->product=new Product();

    }


    public function testGetClientType()
    {
        $value='Association';

        $responseType=$this->clientType->setName($value);
        $response=$this->price->setClientType($responseType);

        self::assertInstanceOf(ClientType::class,$responseType);
        self::assertInstanceOf(Price::class,$response);
        self::assertInstanceOf(ClientType::class,$this->price->getClientType());
        self::assertEquals($value,$this->price->getClientType()->getName());

    }

    public function testGetPrice()
    {
        $value='0.6';

        $response=$this->price->setPrice($value);

        self::assertInstanceOf(Price::class,$response);
        self::assertEquals(floatval($value),floatval($this->price->getPrice()));

    }

    public function testGetProduct()
    {
        $value='Snickers';

        $responseP=$this->product->setName($value);
        $response=$this->price->setProduct($responseP);

        self::assertInstanceOf(Product::class,$responseP);
        self::assertInstanceOf(Price::class,$response);
        self::assertInstanceOf(Product::class,$this->price->getProduct());
        self::assertEquals($value,$this->price->getProduct()->getName());


    }
}
