<?php

namespace App\Tests\Unit\Manager;

use App\Entity\Product;
use App\Entity\Purchase;
use App\Manager\PurchaseManager;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;

class PurchaseManagerTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->product=new Product();
        $this->purchase=new Purchase;
    }


    public function testVerifyDisponibilityProduct()
    {
        $value=1;
        $this->product->setName('Snickers');
        $this->product->setQuantityStock(10);
        $this->purchase->setProduct($this->product);
        $this->purchase->setQuantity(5);

        $purchases=$this
            ->getMockBuilder("App\Manager\PurchaseManager")
            ->disableOriginalConstructor()
            ->getMock();
        $purchases
            ->expects($this->any())
            ->method('verifyDisponibiltyProduct')
            ->with($this->purchase)
            ->will($this->returnValue($value));

        //not working yet will not continue on it today
       // $purchaseManager=new PurchaseManager($manager);
        //self::assertEquals(1,$purchaseManager->verifyDisponibilityProduct($this->purchase));


    }
}
