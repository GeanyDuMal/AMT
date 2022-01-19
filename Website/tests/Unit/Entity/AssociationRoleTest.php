<?php

namespace App\Tests\Unit\Entity\Entity\Entity\Entity\Entity\Entity\Entity\Entity\Entity\Entity;

use App\Entity\AssociationRole;
use PHPUnit\Framework\TestCase;

class AssociationRoleTest extends TestCase
{
    protected function setUp():void
    {
        parent::setUp();
        $this->associationRole=new AssociationRole();
    }


    public function testGetName()
    {
        $value = 'Etudiant';

        $response = $this->associationRole->setName($value);

        self::assertInstanceOf(AssociationRole::class,$response);
        self::assertEquals($value, $this->associationRole->getName());
    }
}
