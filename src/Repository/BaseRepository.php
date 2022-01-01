<?php

namespace App\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

abstract class BaseRepository extends ServiceEntityRepository
{
    public function updateBy(array $where, array $values): void
    {
        $qb = $this->createQueryBuilder('e')
            ->update();

        foreach ($values as $property => $value) {
            $qb->set('e.'.$property, $value);
        }

        $qb->where('1 = 1');

        foreach ($where as $property => $value) {
            $qb->andWhere(sprintf('e.%s = :%s', $property, $property))
                ->setParameter(':'.$property, $value);
        }

        $qb->getQuery()
            ->getResult();
    }

    public function deleteBy(array $data): void
    {
        $qb = $this->createQueryBuilder('e')
            ->delete()
            ->where('1 = 1');

        foreach ($data as $property => $value) {
            $qb->andWhere(sprintf('e.%s = :%s', $property, $property))
                ->setParameter(':'.$property, $value);
        }

        $qb->getQuery()
            ->getResult();
    }
}