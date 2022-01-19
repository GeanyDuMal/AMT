<?php

namespace App\Tests\Unit\Entity;

use App\Entity\Association;
use App\Entity\AssociationRole;
use App\Entity\Client;
use PHPUnit\Framework\TestCase;

class AssociationTest extends TestCase
{
    protected function setUp():void
    {
        parent::setUp();
        $this->association = new Association();
        $this->client= new Client();
        $this->associationRole = new AssociationRole();

    }


    public function testGetRole()
    {
        $value='Etudiant';


        $responseAR=$this->associationRole->setName($value);
        $response=$this->association->setRole($responseAR);

        self::assertInstanceOf(Association::class,$response);
        self::assertInstanceOf(AssociationRole::class,$responseAR);
        self::assertInstanceOf(AssociationRole::class,$this->association->getRole());
        self::assertEquals($value,$this->association->getRole()->getName());


    }

    public function testGetMember()
    {
        $value='Younes';

        $responseM=$this->client->setName($value);
        $response=$this->association->setMember($responseM);

        self::assertInstanceOf(Association::class,$response);
        self::assertInstanceOf(Client::class,$responseM);
        self::assertInstanceOf(Client::class,$this->association->getMember());
        self::assertEquals($value,$this->association->getMember()->getName());

    }

}
