<?php

namespace App\Repository;

use App\Entity\Dictionary;
use App\Repository\BaseRepository;
use App\Repository\Interface\DictionaryRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Dictionary|null find($id, $lockMode = null, $lockVersion = null)
 * @method Dictionary|null findOneBy(array $criteria, array $orderBy = null)
 * @method Dictionary[]    findAll()
 * @method Dictionary[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DictionaryRepository extends BaseRepository implements DictionaryRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Dictionary::class);
    }
}
