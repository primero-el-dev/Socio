<?php

namespace App\Repository;

use ApiPlatform\Core\Api\IriConverterInterface;
use App\Entity\Interface\HasMediaObjects;
use App\Entity\MediaObject;
use App\Repository\BaseRepository;
use App\Repository\Interface\MediaObjectRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method MediaObject|null find($id, $lockMode = null, $lockVersion = null)
 * @method MediaObject|null findOneBy(array $criteria, array $orderBy = null)
 * @method MediaObject[]    findAll()
 * @method MediaObject[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MediaObjectRepository extends BaseRepository implements MediaObjectRepositoryInterface
{
    public function __construct(
        ManagerRegistry $registry,
        private IriConverterInterface $iriConverter
    )
    {
        parent::__construct($registry, MediaObject::class);
    }

    public function findByOwner(HasMediaObjects $object): array
    {
        return $this->findBy([
            'owner_iri' => $this->iriConverter->getIriFromItem($object),
        ]);
    }
}
