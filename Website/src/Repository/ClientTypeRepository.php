<?php

namespace App\Repository;

use App\Entity\ClientType;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ClientType|null find($id, $lockMode = null, $lockVersion = null)
 * @method ClientType|null findOneBy(array $criteria, array $orderBy = null)
 * @method ClientType[]    findAll()
 * @method ClientType[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ClientTypeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ClientType::class);
    }

    // /**
    //  * @return ClientType[] Returns an array of ClientType objects
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
    public function findOneBySomeField($value): ?ClientType
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
