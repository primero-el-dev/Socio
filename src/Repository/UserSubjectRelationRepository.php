<?php

namespace App\Repository;

use ApiPlatform\Core\Api\IriConverterInterface;
use App\Entity\Entity;
use App\Entity\User;
use App\Entity\UserSubjectRelation;
use App\Repository\Interface\UserSubjectRelationRepositoryInterface;
use App\Util\EntityUtils;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method UserSubjectRelation|null find($id, $lockMode = null, $lockVersion = null)
 * @method UserSubjectRelation|null findOneBy(array $criteria, array $orderBy = null)
 * @method UserSubjectRelation[]    findAll()
 * @method UserSubjectRelation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserSubjectRelationRepository extends ServiceEntityRepository 
implements UserSubjectRelationRepositoryInterface
{
    public function __construct(
        ManagerRegistry $registry,
        private IriConverterInterface $iriConverter
    )
    {
        parent::__construct($registry, UserSubjectRelation::class);
    }

    public function findForSubject(Entity $subject): array
    {
        return $this->findBy([
            'subjectIri' => $this->iriConverter->getIriFromItem($subject),
        ]);
    }

    public function isObjectIriReportedByUser(string $iri, User $user): bool
    {
        return !empty($this->findBy([
            'who' => $user->getId(),
            'subjectIri' => $iri,
            'action' => 'REPORT',
        ]));
    }

    public function userCanOn(
        User $user, 
        string $action, 
        ?Entity $subject,
        bool $default = true
    ): bool
    {
        if (!$subject) {
            return $default;
        }

        return $this->userCanOnSubjectIri(
            $user, 
            $action, 
            $this->iriConverter->getIriFromItem($subject), 
            $default
        );
    }

    public function userHasRelationWith(
        User $user, 
        string $action, 
        ?Entity $subject,
        bool $default = true
    ): bool
    {
        if (!$subject) {
            return $default;
        }

        return $this->userCanOnSubjectIri(
            $user, 
            $action, 
            $this->iriConverter->getIriFromItem($subject), 
            $default
        );
    }

    public function userCanOnSubjectIri(
        User $user, 
        string $action, 
        string $subjectIri,
        bool $default = true
    ): bool
    {
        $relations = $this->findBy([
            'action' => $action,
            'subjectIri' => $subjectIri,
        ]);

        if (!$relations) {
            return $default;
        }

        return !empty(array_filter(
            $relations, 
            fn($rel) => !$rel->isDeniedForUser($user)
        ));
    }

    public function deleteWhere(
        int $userId, 
        string $action, 
        string $subjectIri
    ): void
    {
        $this->createQueryBuilder('r')
            ->delete()
            ->where('r.who = :userId')
            ->andWhere('r.action = :action')
            ->andWhere('r.subjectIri = :subjectIri')
            ->setParameter('userId', $userId)
            ->setParameter('action', $action)
            ->setParameter('subjectIri', $subjectIri)
            ->getQuery()
            ->getResult();
    }

    public function getAdminsForIri(string $subjectIri): array
    {
        $relation = $this->findBy([
            'action' => 'ROLE_ADMIN',
            'subjectIri' => $subjectIri,
        ]);

        return array_map(fn($rel) => $rel->getWho(), $relation);
    }

    public function getAdminsFor(Entity $entity): array
    {
        return $this->getAdminsForIri($this->iriConverter->getIriFromItem($entity));
    }
}
