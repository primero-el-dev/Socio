<?php

namespace App\Repository;

use App\Entity\Token;
use App\Entity\User;
use App\Repository\BaseRepository;
use App\Repository\Interface\TokenRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Token|null find($id, $lockMode = null, $lockVersion = null)
 * @method Token|null findOneBy(array $criteria, array $orderBy = null)
 * @method Token[]    findAll()
 * @method Token[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TokenRepository extends BaseRepository implements TokenRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Token::class);
    }

    public function getTokenByTypeAndValue(string $type, string $value): ?Token
    {
        return $this->findOneBy([
            'type' => $type,
            'value' => $value,
        ]);
    }

    public function getTokenByTypeValueUser(string $type, string $value, User $user): ?Token
    {
        return $this->findOneBy([
            'type' => $type,
            'value' => $value,
            'user' => $user->getId(),
        ]);
    }

    public function isValid(string $type, string $value): bool
    {
        $token = $this->findOneBy([
            'type' => $type,
            'value' => $value,
        ]);

        return ($token && !$token->hasExpired());
    }

    public function deleteByTypeAndUserId(string $type, int $userId): void
    {
        $this->deleteBy([
            'type' => $type,
            'user' => $userId,
        ]);
    }
}
