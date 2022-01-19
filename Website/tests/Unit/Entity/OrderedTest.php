<?php

namespace App\Tests\Unit;

use App\Entity\Client;
use App\Entity\Ordered;
use App\Entity\PaymentType;
use DateTime;
use PHPUnit\Framework\TestCase;

class OrderedTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->ordered=new Ordered();
        $this->dateTime=new DateTime('NOW');
        $this->paymentType=new PaymentType();
        $this->client=new Client();
    }


    public function testGetClient()
    {
        $value='Younes';

        $responseC=$this->client->setName($value);
        $response=$this->ordered->setClient($responseC);

        self::assertInstanceOf(Client::class, $responseC);
        self::assertInstanceOf(Client::class, $this->ordered->getClient());
        self::assertInstanceOf(Ordered::class, $response);
        self::assertEquals($value, $this->ordered->getClient()->getName());
    }

    public function testGetPaymentType()
    {
        $value='CB';

        $responsePT=$this->paymentType->setName($value);
        $response=$this->ordered->setPaymentType($responsePT);

        self::assertInstanceOf(PaymentType::class,$responsePT);
        self::assertInstanceOf(PaymentType::class,$response->getPaymentType());
        self::assertInstanceOf(Ordered::class,$response);
        self::assertEquals($value,$this->ordered->getPaymentType()->getName());

    }

    public function testGetOrderedAt()
    {
        $value= $this->dateTime;

        $response=$this->ordered->setOrderedAt($value);

        self::assertInstanceOf(Ordered::class,$response);
        self::assertInstanceOf(DateTime::class,$value);
        self::assertEquals($value,$this->ordered->getOrderedAt());

    }
}
