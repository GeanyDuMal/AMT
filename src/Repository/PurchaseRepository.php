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

    /**
     * @param Product $product
     * @return int Number of purchase for the $product
     */
    public function getQuantityByProduct(Product $product): int
    {
        return $this->createQueryBuilder('a')
            ->select("SUM( a.quantity ) as somme")
            ->andWhere("a.product=:id")
            ->setParameter('id', $product->getId())
            ->getQuery()
            ->getResult();
    }

    /**
     * @return int Amount of sales for this month
     */
    public function salesRevenueThisMonth(): int
    {
        $thisMonth = date('m');
        $purchase = $this->getEntityManager()->createQuery("
            SELECT SUM(Price.price*Purchase.quantity) as revenue
            FROM App\Entity\Price Price, App\Entity\Purchase Purchase, App\Entity\Ordered Ordered, App\Entity\ClientType ClientType
            WHERE Purchase.ordered = Ordered.id
            AND Purchase.product = Price.product
            AND Price.clientType = 'Association'
            AND MONTH(Ordered.orderedAt)=$thisMonth
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
