<?php

namespace App\Repository;

use App\Entity\Client;
use DateInterval;
use DateTime;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Client|null find($id, $lockMode = null, $lockVersion = null)
 * @method Client|null findOneBy(array $criteria, array $orderBy = null)
 * @method Client[]    findAll()
 * @method Client[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ClientRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Client::class);
    }

    /**
     * @return Client[] Returns an array of Client that hasn't ordered anything for 2 years and have a 2 years old account
     */
    public function findClientWithoutOrderedTwoYears(): array
    {
        $date = new DateTime();
        $date = $date->sub(DateInterval::createFromDateString("2 Year"));

        //Recupere tout les clients qui n'ont pas une commande de moins de 2 ans
        $orderedQuery = $this->getEntityManager()->createQuery("
            SELECT Client
            FROM App\Entity\Client Client
            WHERE Client NOT IN (
                SELECT ClientInOrdered
                FROM App\Entity\Ordered Ordered, App\Entity\Client ClientInOrdered
                WHERE Ordered.client = ClientInOrdered
                AND Ordered.orderedAt >= :date
            )
            AND Client.creationDate <= :date
            ")
            ->setParameter("date", $date);

        return $orderedQuery->getResult();
    }

    // /**
    //  * @return Client[] Returns an array of Client objects
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
    public function findOneBySomeField($value): ?Client
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
