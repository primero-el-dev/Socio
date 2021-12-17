<?php

namespace App\Security\Voter\Relation;

use App\Entity\UserSubjectRelation;
use App\Security\Voter\Relation\RelationVoter;

class MotherRelationVoter extends RelationVoter
{
    protected function getRoles(): array
    {
        return [
            'request_relation' => 'REQUEST_MOTHER_RELATION',
            'accept_relation' => 'ACCEPT_MOTHER_RELATION',
            'break_relation' => 'BREAK_MOTHER_RELATION',
        ];
    }

    protected function getRelationRequest(): string
    {
        return UserSubjectRelation::REQUEST_MOTHER;
    }

    protected function getRealizedRelation(): string
    {
        return UserSubjectRelation::MOTHER;
    }
}