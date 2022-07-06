<?php

namespace App\Tests\Unit\Entity;

use App\Entity\Ordered;
use App\Entity\Product;
use App\Entity\Purchase;
use DateTime;
use PHPUnit\Framework\TestCase;

class PurchaseTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->purchase = new Purchase();
        $this->product = new Product();
        $this->ordered = new Ordered();
        $this->dateTime = new DateTime('NOW');
    }

    public function testGetProduct()
    {
        $value = 'Snickers';

        $responseP = $this->product->setName($value);
        $response = $this->purchase->setProduct($responseP);

        self::assertInstanceOf(Product::class, $responseP);
        self::assertInstanceOf(Product::class, $response->getProduct());
        self::assertInstanceOf(Purchase::class, $response);
        self::assertEquals($value, $this->purchase->getProduct()->getName());
    }

    public function testGetQuantity()
    {
        $value = 10;

        $response = $this->purchase->setQuantity($value);

        self::assertInstanceOf(Purchase::class, $response);
        self::assertEquals($value, $this->purchase->getQuantity());

    }

    public function testGetOrdered()
    {
        $value = $this->dateTime;

        $responseO = $this->ordered->setOrderedAt($value);
        $response = $this->purchase->setOrdered($responseO);

        self::assertInstanceOf(Ordered::class, $responseO);
        self::assertInstanceOf(DateTime::class, $value);
        self::assertInstanceOf(Ordered::class, $response->getOrdered());
        self::assertInstanceOf(Purchase::class, $response);
        self::assertEquals($value, $this->purchase->getOrdered()->getOrderedAt());

    }
}
