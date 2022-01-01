<?php

namespace App\Security\Voter\Relation;

use App\Entity\UserSubjectRelation;
use App\Security\Voter\Relation\RelationVoter;

class FatherRelationVoter extends RelationVoter
{
    protected function getRoles(): array
    {
        return [
            'request_relation' => 'REQUEST_FATHER_RELATION',
            'accept_relation' => 'ACCEPT_FATHER_RELATION',
            'break_relation' => 'BREAK_FATHER_RELATION',
        ];
    }

    protected function getRelationRequest(): string
    {
        return UserSubjectRelation::REQUEST_FATHER;
    }

    protected function getRealizedRelation(): string
    {
        return UserSubjectRelation::FATHER;
    }
}