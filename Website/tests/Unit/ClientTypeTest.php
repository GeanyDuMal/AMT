<?php

namespace App\Tests\Unit;

use App\Entity\ClientType;
use PHPUnit\Framework\TestCase;

class ClientTypeTest extends TestCase
{
    protected function setUp():void
    {
        parent::setUp();
        $this->clientType= new ClientType();
    }


    public function testGetName()
    {
        $value='Association';
        $id=null;

        $response=$this->clientType->setName($value);

        self::assertInstanceOf(ClientType::class,$response);
        self::assertEquals($value, $this->clientType->getName());
        self::assertEquals($id,$this->clientType->getId());

    }

}
