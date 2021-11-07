<?php

namespace App\Repository;

use App\Entity\Relation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Relation|null find($id, $lockMode = null, $lockVersion = null)
 * @method Relation|null findOneBy(array $criteria, array $orderBy = null)
 * @method Relation[]    findAll()
 * @method Relation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RelationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Relation::class);
    }

    // /**
    //  * @return Relation[] Returns an array of Dictionary objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('d.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Dictionary
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
