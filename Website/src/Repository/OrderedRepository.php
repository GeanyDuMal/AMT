<?php

namespace App\Repository;

use App\Entity\Ordered;
use DateInterval;
use DateTime;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use DoctrineExtensions\Query\Mysql\Year;
use DoctrineExtensions\Query\Sqlite\Date;

/**
 * @method Ordered|null find($id, $lockMode = null, $lockVersion = null)
 * @method Ordered|null findOneBy(array $criteria, array $orderBy = null)
 * @method Ordered[]    findAll()
 * @method Ordered[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class OrderedRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Ordered::class);
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
            ->addSelect('c')
            ->getQuery()
            ->getResult();
    }

    public function quantityThisWeeksCommands()
    {
        $thisWeek = date('W');
        return $this->createQueryBuilder('a')
            ->select("count(a) as number")
            ->where("WEEK(a.orderedAt)=:thisWeek")
            ->setParameter("thisWeek", $thisWeek)
            ->getQuery()
            ->getResult()[0];
    }

    public function thisWeekOrdered()
    {
        $thisWeek = date('W');
        $purchase = $this->getEntityManager()->createQuery("
            SELECT Ordered
            FROM App\Entity\Ordered Ordered
            WHERE WEEK(Ordered.orderedAt) = $thisWeek
            ");
        return $purchase->getResult();
    }

    public function thisMonthOrdered()
    {
        $thisMonth = date('m');
        $purchase = $this->getEntityManager()->createQuery("
            SELECT Ordered
            FROM App\Entity\Ordered Ordered
            WHERE MONTH(Ordered.orderedAt) = $thisMonth
            ");
        return $purchase->getResult();
    }

    public function thisYearOrdered()
    {
        $thisYear = date('Y');
        $purchase = $this->getEntityManager()->createQuery("
            SELECT Ordered
            FROM App\Entity\Ordered Ordered
            WHERE YEAR(Ordered.orderedAt) = $thisYear
            ");
        return $purchase->getResult();
    }

    /**
     * @return Ordered[] return an array of Ordered that are 2 years old and doesn't have client assigned
     */
    public function findOrderWithoutClientTwoYearsOld(): array
    {
        $date = new DateTime();
        $date = $date->sub(DateInterval::createFromDateString("2 Year"));
        $ordered = $this->getEntityManager()->createQuery("
            SELECT Ordered
            FROM App\Entity\Ordered Ordered
            WHERE Ordered.client IS NULL
            AND Ordered.orderedAt < :date
        ")
        ->setParameter("date", $date);

        return $ordered->getResult();
    }


    // /**
    //  * @return Ordered[] Returns an array of Ordered objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('o')
            ->andWhere(o.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('o.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Order
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */


}
