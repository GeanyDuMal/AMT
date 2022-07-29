<?php

namespace App\Tests\Unit\Entity;

use App\Entity\Association;
use App\Entity\Client;
use App\Utils\Enum\AssociationRole;
use App\Utils\Enum\ClientType;
use PHPUnit\Framework\TestCase;

class AssociationTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->association = new Association();
        $this->client = new Client();
    }


    public function testGetRole()
    {
        $role = AssociationRole::MEMBRE;
        $response = $this->association->setRole($role);

        self::assertInstanceOf(Association::class, $response);
        self::assertContains($this->association->getRole(), AssociationRole::getAll());
        self::assertEquals($role, $this->association->getRole());
    }

    public function testGetMember()
    {
        $name = 'Younes';

        $responseM = $this->client->setName($name);
        $response = $this->association->setMember($responseM);

        self::assertInstanceOf(Association::class, $response);
        self::assertInstanceOf(Client::class, $responseM);
        self::assertInstanceOf(Client::class, $this->association->getMember());
        self::assertEquals($name, $this->association->getMember()->getName());
    }

}
