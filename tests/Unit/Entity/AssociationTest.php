<?php

namespace App\Tests\Unit\Entity;

use App\Entity\Member;
use App\Entity\Client;
use App\Utils\Enum\MemberRole;
use App\Utils\Enum\ClientType;
use PHPUnit\Framework\TestCase;

class AssociationTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->association = new Member();
        $this->client = new Client();
    }


    public function testGetRole()
    {
        $role = MemberRole::MEMBRE;
        $response = $this->association->setRole($role);

        self::assertInstanceOf(Member::class, $response);
        self::assertContains($this->association->getRole(), MemberRole::getAll());
        self::assertEquals($role, $this->association->getRole());
    }

    public function testGetMember()
    {
        $name = 'Younes';

        $responseM = $this->client->setName($name);
        $response = $this->association->setClient($responseM);

        self::assertInstanceOf(Member::class, $response);
        self::assertInstanceOf(Client::class, $responseM);
        self::assertInstanceOf(Client::class, $this->association->getClient());
        self::assertEquals($name, $this->association->getClient()->getName());
    }

}
