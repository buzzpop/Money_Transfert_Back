<?php

namespace App\Repository;

use App\Entity\Comissions;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Comissions|null find($id, $lockMode = null, $lockVersion = null)
 * @method Comissions|null findOneBy(array $criteria, array $orderBy = null)
 * @method Comissions[]    findAll()
 * @method Comissions[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ComissionsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Comissions::class);
    }

    // /**
    //  * @return Comissions[] Returns an array of Comissions objects
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
    public function findOneBySomeField($value): ?Comissions
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
