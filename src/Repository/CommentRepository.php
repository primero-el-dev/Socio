<?php

namespace App\Repository;

use App\Entity\Comment;
use App\Entity\HasComments;
use App\Repository\BaseRepository;
use App\Repository\Interface\CommentRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Comment|null find($id, $lockMode = null, $lockVersion = null)
 * @method Comment|null findOneBy(array $criteria, array $orderBy = null)
 * @method Comment[]    findAll()
 * @method Comment[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CommentRepository extends BaseRepository implements CommentRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Comment::class);
    }

    public function getCommentsForParent(Comment $parent): array
    {
        return $this->findBy([
            'parent_id' => $parent->getId(),
        ]);
    }
}
