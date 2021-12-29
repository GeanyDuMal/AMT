<?php

namespace App\Repository;

use App\Entity\Command;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Command|null find($id, $lockMode = null, $lockVersion = null)
 * @method Command|null findOneBy(array $criteria, array $orderBy = null)
 * @method Command[]    findAll()
 * @method Command[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CommandRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Command::class);
    }
    public function CountByDate()
    {
        return $this->createQueryBuilder('a')
            ->select("SUBSTRING(a.orderedAt,1,10) as orderDate,count(a) as count")
            ->groupBy('orderDate')
            ->getQuery()
            ->getResult()
            ;
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
