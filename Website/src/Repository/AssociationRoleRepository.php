<?php

namespace App\Repository;

use App\Entity\AssociationRole;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method AssociationRole|null find($id, $lockMode = null, $lockVersion = null)
 * @method AssociationRole|null findOneBy(array $criteria, array $orderBy = null)
 * @method AssociationRole[]    findAll()
 * @method AssociationRole[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AssociationRoleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AssociationRole::class);
    }

    // /**
    //  * @return AssociationRole[] Returns an array of AssociationRole objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('a.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?AssociationRole
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
