<?php

namespace App\Repository;

use App\Entity\Comment;
use App\Entity\Reaction;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Reaction|null find($id, $lockMode = null, $lockVersion = null)
 * @method Reaction|null findOneBy(array $criteria, array $orderBy = null)
 * @method Reaction[]    findAll()
 * @method Reaction[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ReactionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Reaction::class);
    }

    public function deleteForUserAndComment(User $user, Comment $comment): void
    {
        $qb = $this->createQueryBuilder('r')
            ->delete()
            ->where('r.comment = :commentId')
            ->andWhere('r.author = :authorId')
            ->setParameter('commentId', $comment->getId())
            ->setParameter('authorId', $user->getId())
            ->getQuery()
            ->getResult();
    }

    public function getForUserAndComment(User $user, Comment $comment): ?Reaction
    {
        return $this->createQueryBuilder('r')
            ->where('r.comment = :commentId')
            ->andWhere('r.author = :authorId')
            ->setParameter('commentId', $comment->getId())
            ->setParameter('authorId', $user->getId())
            ->getQuery()
            ->getOneOrNullResult();
    }

    // /**
    //  * @return Reaction[] Returns an array of Reaction objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('r.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Reaction
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}