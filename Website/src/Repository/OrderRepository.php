<?php

namespace App\Repository;

use App\Entity\Order;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Order|null find($id, $lockMode = null, $lockVersion = null)
 * @method Order|null findOneBy(array $criteria, array $orderBy = null)
 * @method Order[]    findAll()
 * @method Order[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class OrderRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Order::class);
    }


    public function countByDate()
    {
        return $this->createQueryBuilder('a')
            ->select("SUBSTRING(a.orderedAt,1,10) as orderDate,count(a) as count")
            ->groupBy('orderDate')
            ->getQuery()
            ->getResult();
    }

    public function findAllOrderAndClientAndClientType()
    {
        return $this->createQueryBuilder('o')
            ->leftJoin('o.client', 'c')
            ->leftjoin('c.clientType', 't')
            ->addSelect('c')
            ->addSelect('t')
            ->getQuery()
            ->getResult();
    }
    public function thisWeeksCommands()
    {
        $thisWeek =date('W');
        return $this->createQueryBuilder('a')
            ->select("count(a) as number")
            ->where("WEEK(a.orderedAt)=:thisWeek")
            ->setParameter("thisWeek",$thisWeek)
            ->getQuery()
            ->getResult()[0]
            ;

    }
    // /**
    //  * @return Order[] Returns an array of Order objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('c.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Order
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */


}
