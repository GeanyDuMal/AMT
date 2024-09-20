<?php

namespace App\Repository;

use App\Entity\Ordered;
use DateInterval;
use DateTime;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

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
        return $this->createQueryBuilder("a")
            ->select("SUBSTRING(a.orderedAt,1,10) as orderDate,count(a) as count")
            ->groupBy("orderDate")
            ->getQuery()
            ->getResult();
    }

    /**
     * @return Ordered[] with Client loaded
     */
    public function findAllOrderAndClientAndClientType(): array
    {
        return $this->createQueryBuilder("o")
            ->leftJoin("o.client", "c")
            ->addSelect("c")
            ->getQuery()
            ->getResult();
    }

    /**
     * @return array number of Ordered this week
     */
    public function quantityThisWeeksCommands(): array
    {
        $thisWeek = date("W");
        $thisYear = date("Y");

        return $this->createQueryBuilder("a")
            ->select("count(a) as number")
            ->where("WEEK(a.orderedAt) = :thisWeek")
            ->andWhere("YEAR(a.orderedAt) = :thisYear")
            ->setParameter("thisWeek", $thisWeek)
            ->setParameter("thisYear", $thisYear)
            ->getQuery()
            ->getResult()[0];
    }

    /**
     * @return Ordered[] done this week
     */
    public function thisWeekOrdered(): array {
        $thisWeek = date("W");
        $thisMonth = date("m");
        $thisYear = date("Y");

        $purchase = $this->getEntityManager()->createQuery("
            SELECT Ordered
            FROM App\Entity\Ordered Ordered
            WHERE WEEK(Ordered.orderedAt) = $thisWeek
            AND MONTH(Ordered.orderedAt) = $thisMonth
            AND YEAR(Ordered.orderedAt) = $thisYear
            ");
        return $purchase->getResult();
    }

    /**
     * @return Ordered[] done this month
     */
    public function thisMonthOrdered(): array {
        $thisMonth = date("m");
        $thisYear = date("Y");

        $purchase = $this->getEntityManager()->createQuery("
            SELECT Ordered
            FROM App\Entity\Ordered Ordered
            WHERE MONTH(Ordered.orderedAt) = $thisMonth
            AND YEAR(Ordered.orderedAt) = $thisYear
            ");
        return $purchase->getResult();
    }

    /**
     * @return Ordered[] done this year
     */
    public function thisYearOrdered(): array {
        $thisYear = date("Y");

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

        return $this->createQueryBuilder("o")
            ->where("o.client IS NULL")
            ->andWhere("o.orderedAt < :date")
            ->setParameter("date", $date)
            ->getQuery()
            ->getResult();
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
