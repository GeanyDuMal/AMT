<?php

namespace App\Repository;

use App\Entity\Product;
use DateInterval;
use DateTime;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Product|null find($id, $lockMode = null, $lockVersion = null)
 * @method Product|null findOneBy(array $criteria, array $orderBy = null)
 * @method Product[]    findAll()
 * @method Product[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProductRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Product::class);
    }

    /**
     * @return Product[] Returns an array of Product objects
     * Return all the product with a positive stock
     */
    public function findAllPositiveStock(): array
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.quantityStock > 0')
            ->orderBy('p.productType, p.name', 'ASC')
            ->getQuery()
            ->getResult()
            ;
    }

    /**
     * @return Product[] Returns an array of Product objects
     * Return all the product with a positive stock
     */
    public function findAllEmptyStock(): array
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.quantityStock = 0')
            ->orderBy('p.productType, p.name', 'ASC')
            ->getQuery()
            ->getResult()
            ;
    }

    /**
     * @return Product[]
     */
    public function findProductEmptyWithoutCommandOneYear(): array
    {
        $date = new DateTime();
        $date = $date->sub(DateInterval::createFromDateString("1 Year"));

        //Recupere tout les produits qui n'ont pas une commande de moins de 1 an et un stock vide
        $productQuery = $this->getEntityManager()->createQuery("
            SELECT Product_0
            FROM App\Entity\Product Product_0
            WHERE Product_0 NOT IN (
                SELECT Product_1
                FROM App\Entity\Ordered Ordered, App\Entity\Purchase Purchase, App\Entity\Product Product_1
                WHERE Purchase.ordered = Ordered
                AND Purchase.product = Product_1
                AND Ordered.orderedAt >= :date
                )
            AND Product_0.quantityStock = 0
            ")
            ->setParameter("date", $date);

        return $productQuery->getResult();
    }

    // /**
    //  * @return Product[] Returns an array of Product objects
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
    public function findOneBySomeField($value): ?Product
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
