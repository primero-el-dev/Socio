<?php

namespace App\Validator;

use App\Validator\UniqueIgnoreDeleted;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Doctrine\ORM\EntityManagerInterface;

class UniqueIgnoreDeletedValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint)
    {
        if (!$constraint instanceof UniqueIgnoreDeleted) {
            throw new UnexpectedTypeException($constraint, UniqueIgnoreDeleted::class);
        }

        if (null === $value || '' === $value) {
            return;
        }

        $repository = $constraint->entityManager->getRepository($constraint->className);
        $result = $repository->findOneBy([
            $constraint->valueColumn => $value,
            $constraint->deletionColumn => null,
        ]);

        if ($result) {
            $this->context->buildViolation($constraint->message)
            ->setParameter('{{ value }}', $value)
            ->addViolation();
        }
    }
}
