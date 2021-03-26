<?php

namespace App\Repository;

use App\Entity\Transaction;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Transaction|null find($id, $lockMode = null, $lockVersion = null)
 * @method Transaction|null findOneBy(array $criteria, array $orderBy = null)
 * @method Transaction[]    findAll()
 * @method Transaction[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TransactionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Transaction::class);
    }

    // /**
    //  * @return Transaction[] Returns an array of Transaction objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('t.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Transaction
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
    public function getCommissionDepot(int $id)
    {
        return $this->createQueryBuilder('t')
            ->innerJoin('t.account_depot', 'acc')
            ->innerJoin('acc.agency', 'a')
            ->andWhere('a.id = :value')
            ->setParameter('value' , $id)
            ->getQuery()
            ->getResult()
            ;
    }

    public function getCommissionRetrait(int  $id)
    {
        return $this->createQueryBuilder('t')
            ->innerJoin('t.account_retrait', 'acc')
            ->innerJoin('acc.agency', 'a')
            ->andWhere('a.id = :value')
            ->setParameter('value' , $id)
            ->getQuery()
            ->getResult()
            ;
    }
    public function getTransDepotByUser(int  $id)
    {
        return $this->createQueryBuilder('t')
            ->innerJoin('t.userDepot', 'u')
            ->andWhere('u.id= :value')
            ->setParameter('value' , $id)
            ->getQuery()
            ->getResult()
            ;
    }

    public function getTransRetraitByUser(int  $id)
    {
        return $this->createQueryBuilder('t')
            ->innerJoin('t.userRetrait', 'u')
            ->andWhere('u.id= :value')
            ->setParameter('value' , $id)
            ->getQuery()
            ->getResult()
            ;
    }

    public function transactionAgence(int $id)
    {
        return $this->createQueryBuilder('t')
            ->innerJoin('t.account_depot', 'ad')
            ->innerJoin('t.account_retrait', 'ar')
            ->innerJoin('ad.agency', 'a')
            ->innerJoin('ar.agency', 'ag')
            ->andWhere('a.id= :value')
            ->andWhere('ag.id= :val')
            ->setParameter('value' , $id)
            ->setParameter('val' , $id)
            ->getQuery()
            ->getResult()
            ;
    }

}
