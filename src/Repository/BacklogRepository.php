<?php

namespace App\Repository;

use App\Entity\Backlog;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Backlog|null find($id, $lockMode = null, $lockVersion = null)
 * @method Backlog|null findOneBy(array $criteria, array $orderBy = null)
 * @method Backlog[]    findAll()
 * @method Backlog[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BacklogRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Backlog::class);
    }

    // /**
    //  * @return Backlog[] Returns an array of Backlog objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('b.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Backlog
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
