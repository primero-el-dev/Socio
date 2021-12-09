<?php

namespace App\Repository;

use App\Entity\Thread;
use App\Repository\Interface\ThreadRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Thread|null find($id, $lockMode = null, $lockVersion = null)
 * @method Thread|null findOneBy(array $criteria, array $orderBy = null)
 * @method Thread[]    findAll()
 * @method Thread[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ThreadRepository extends ServiceEntityRepository implements ThreadRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Thread::class);
    }
}
