<?php

namespace App\Repository;

use App\Entity\Comment;
use App\Entity\Reaction;
use App\Entity\User;
use App\Repository\BaseRepository;
use App\Repository\Interface\ReactionRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Reaction|null find($id, $lockMode = null, $lockVersion = null)
 * @method Reaction|null findOneBy(array $criteria, array $orderBy = null)
 * @method Reaction[]    findAll()
 * @method Reaction[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ReactionRepository extends BaseRepository implements ReactionRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Reaction::class);
    }

    public function deleteForUserAndComment(User $user, Comment $comment): void
    {
        $this->deleteBy([
            'comment' => $comment->getId(),
            'author' => $user->getId(),
        ]);
    }

    public function getForUserAndComment(User $user, Comment $comment): ?Reaction
    {
        return $this->findBy([
            'comment' => $comment->getId(),
            'author' => $user->getId(),
        ]);
    }
}
