<?php

namespace App\Tests\Unit\Entity;

use App\Entity\Client;
use App\Entity\Ordered;
use App\Utils\Enum\PaymentTypeEnum;
use DateTime;
use PHPUnit\Framework\TestCase;

class OrderedTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->ordered = new Ordered();
        $this->dateTime = new DateTime('Now');
        $this->client = new Client();
    }


    public function testGetClient()
    {
        $value = 'Younes';

        $responseC = $this->client->setName($value);
        $response = $this->ordered->setClient($responseC);

        self::assertInstanceOf(Client::class, $responseC);
        self::assertInstanceOf(Client::class, $this->ordered->getClient());
        self::assertInstanceOf(Ordered::class, $response);
        self::assertEquals($value, $this->ordered->getClient()->getName());
    }

    public function testGetPaymentType()
    {
        $value = PaymentTypeEnum::CARTE_BANCAIRE;

        $response = $this->ordered->setPaymentType($value);

        self::assertInstanceOf(Ordered::class, $response);
        self::assertContains($this->ordered->getPaymentType(), PaymentTypeEnum::getAll());
        self::assertEquals($value, $this->ordered->getPaymentType());
    }

    public function testGetOrderedAt()
    {
        $value = $this->dateTime;

        $response = $this->ordered->setOrderedAt($value);

        self::assertInstanceOf(Ordered::class, $response);
        self::assertInstanceOf(DateTime::class, $value);
        self::assertEquals($value, $this->ordered->getOrderedAt());

    }
}
