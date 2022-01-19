<?php

namespace App\Tests\Unit\Entity\Entity\Entity\Entity\Entity;

use App\Entity\PaymentType;
use PHPUnit\Framework\TestCase;

class PaymentTypeTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->paymentType=new PaymentType();
    }

    public function testGetName()
    {
        $value='CB';

        $response=$this->paymentType->setName($value);

        self::assertInstanceOf(PaymentType::class,$response);
        self::assertEquals($value,$this->paymentType->getName());

    }
}
