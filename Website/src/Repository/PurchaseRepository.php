<?php

namespace App\Repository;

use App\Entity\Product;
use App\Entity\Purchase;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Purchase|null find($id, $lockMode = null, $lockVersion = null)
 * @method Purchase|null findOneBy(array $criteria, array $orderBy = null)
 * @method Purchase[]    findAll()
 * @method Purchase[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PurchaseRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Purchase::class);
    }
    public function getQuantityByProduct(Product $product)
    {
        return $this->createQueryBuilder('a')
            ->select("SUM( a.quantity ) as somme")
            ->andWhere("a.product=:id")
            ->setParameter('id',$product->getId())
            ->getQuery()
            ->getResult()
            ;
    }
    public function salesRevunueOverAll()
    {
        $purchase=$this->getEntityManager()->createQuery("
            SELECT SUM(Price.price*Purchase.quantity) as revunue
            FROM App\Entity\Price Price,App\Entity\Purchase Purchase
            WHERE Purchase.product=Price.product
        ");
        return $purchase->getResult()[0];
    }

    public function salesRevenueThisWeek()
    {
        $thisWeek =date('W');
        $purchase=$this->getEntityManager()->createQuery("
            SELECT SUM(Price.price*Purchase.quantity) as revunue,WEEK(Command.orderedAt) week
            FROM App\Entity\Price Price,App\Entity\Purchase Purchase,App\Entity\Command Command
            WHERE Purchase.product=Price.product
            AND WEEK(Command.orderedAt)=$thisWeek
            AND Command=Purchase.command
        ");
        return $purchase->getResult()[0];
    }
    public function salesRevenueThisMonth()
    {
        $thisMonth =date('m');
        $purchase=$this->getEntityManager()->createQuery("
            SELECT SUM(Price.price*Purchase.quantity) as revunue
            FROM App\Entity\Price Price,App\Entity\Purchase Purchase,App\Entity\Command Command
            WHERE Purchase.product=Price.product
            AND MONTH(Command.orderedAt)=$thisMonth
            AND Command=Purchase.command
        ");
        return $purchase->getResult()[0];
    }
    // /**
    //  * @return Purchase[] Returns an array of Purchase objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Purchase
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */


}
