<?php

namespace App\Repository;

use App\Entity\MealStyle;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method MealStyle|null find($id, $lockMode = null, $lockVersion = null)
 * @method MealStyle|null findOneBy(array $criteria, array $orderBy = null)
 * @method MealStyle[]    findAll()
 * @method MealStyle[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MealStyleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MealStyle::class);
    }

    // /**
    //  * @return MealStyle[] Returns an array of MealStyle objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('m.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?MealStyle
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
