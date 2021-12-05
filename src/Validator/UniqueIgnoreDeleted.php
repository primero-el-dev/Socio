<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;
use Doctrine\ORM\EntityManagerInterface;

/**
 * @Annotation
 */
class UniqueIgnoreDeleted extends Constraint
{
    /*
     * Any public properties become valid options for the annotation.
     * Then, use these in your validator class.
     */
    public $message = 'The value "{{ value }}" is not valid.';

    public function __construct(
        array $options = null,
        public EntityManagerInterface $entityManager,
        public string $className,
        public string $valueColumn,
        public ?string $deletionColumn = 'deletedAt',
        ?string $message = null,
        array $groups = null,
        $payload = null
    ) {
        parent::__construct($options, $groups, $payload);

        $this->message = $message ?? $this->message;
    }
}
