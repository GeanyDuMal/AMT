<?php

namespace App\Tests\Unit\Entity;

use App\Entity\Client;
use App\Entity\ClientType;
use PHPUnit\Framework\TestCase;

class  ClientTest extends TestCase
{
    private Client $client;
    protected function setUp() :void
    {
        parent::setUp();
        $this->client = new Client();
        $this->clientType= new ClientType();
    }

    public function testGetID(): void
    {
        $value= '1';

        $response=$this->client->setID($value);

        self::assertInstanceOf(Client::class,$response);
        self::assertEquals($value,$this->client->getID());
        self::assertContains('ROLE_USER',$this->client->getRoles());
    }

    public function testGetBalance():void
    {
        $value=100;
        $response=$this->client->setBalance($value);

        self::assertInstanceOf(Client::class,$response);
        self::assertEquals($value,$this->client->getBalance());
    }

    public function testGetFidelityPoint():void
    {
        $value=10;
        $response=$this->client->setFidelityPoint($value);

        self::assertInstanceOf(Client::class,$response);
        self::assertEquals($value,$this->client->getFidelityPoint());
    }

    public function testGetClientType():void
    {
        $value='Association';

        $responseType=$this->clientType->setName($value);
        $response=$this->client->setClientType($responseType);

        self::assertInstanceOf(ClientType::class,$responseType);
        self::assertInstanceOf(Client::class,$response);
        self::assertInstanceOf(ClientType::class,$this->client->getClientType());
        self::assertEquals($value,$this->client->getClientType()->getName());
    }


    public function testGetRole():void
    {
        $value=['ROLE_PRESIDENT'];
        $response=$this->client->setRoles($value);

        self::assertInstanceOf(Client::class,$response);
        self::assertContains('ROLE_USER',$this->client->getRoles());
        self::assertContains('ROLE_PRESIDENT',$this->client->getRoles());
    }

    public function testGetPassword()
    {
        $value='password';
        $response=$this->client->setPassword($value);

        self::assertInstanceOf(Client::class,$response);
        self::assertEquals($value,$this->client->getPassword());

    }


}
