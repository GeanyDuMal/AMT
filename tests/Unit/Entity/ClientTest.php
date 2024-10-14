<?php

namespace App\Tests\Unit\Entity;

use App\Entity\Client;
use App\Utils\Enum\ClientTypeEnum;
use App\Utils\Enum\SymfonyRole;
use PHPUnit\Framework\TestCase;

class  ClientTest extends TestCase
{
    private Client $client;

    protected function setUp(): void
    {
        parent::setUp();
        $this->client = new Client();
    }

    public function testGetID(): void
    {
        $value = '1';

        $response = $this->client->setId($value);

        self::assertInstanceOf(Client::class, $response);
        self::assertEquals($value, $this->client->getID());
        self::assertContains(SymfonyRole::USER, $this->client->getRoles());
    }

    public function testGetBalance(): void
    {
        $value = 100;
        $response = $this->client->setBalance($value);

        self::assertInstanceOf(Client::class, $response);
        self::assertEquals($value, $this->client->getBalance());
    }

    public function testGetFidelityPoint(): void
    {
        $value = 10;
        $response = $this->client->setFidelityPoint($value);

        self::assertInstanceOf(Client::class, $response);
        self::assertEquals($value, $this->client->getFidelityPoint());
    }

    public function testGetClientType(): void
    {
        $value = ClientTypeEnum::ETUDIANT;

        $response = $this->client->setClientType($value);

        self::assertInstanceOf(Client::class, $response);
        self::assertContains($this->client->getClientType(), ClientTypeEnum::getAll());
        self::assertEquals($value, $this->client->getClientType());
    }


    public function testGetRole(): void
    {
        $value = [SymfonyRole::PRESIDENT];
        $response = $this->client->setRoles($value);

        self::assertInstanceOf(Client::class, $response);
        self::assertContains(SymfonyRole::USER, $this->client->getRoles());
        self::assertContains(SymfonyRole::PRESIDENT, $this->client->getRoles());
        foreach ($this->client->getRoles() as $role){
            self::assertContains($role, SymfonyRole::getAll());
        }
    }

    public function testGetPassword()
    {
        $value = 'password';
        $response = $this->client->setPassword($value);

        self::assertInstanceOf(Client::class, $response);
        self::assertEquals($value, $this->client->getPassword());
    }


}
