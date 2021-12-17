<?php

namespace App\Security\Voter\Relation;

use App\Entity\UserSubjectRelation;
use App\Security\Voter\Relation\RelationVoter;

class FriendRelationVoter extends RelationVoter
{
    protected function getRoles(): array
    {
        return [
            'request_relation' => 'REQUEST_FRIEND_RELATION',
            'accept_relation' => 'ACCEPT_FRIEND_RELATION',
            'break_relation' => 'BREAK_FRIEND_RELATION',
        ];
    }

    protected function getRelationRequest(): string
    {
        return UserSubjectRelation::REQUEST_FRIEND;
    }

    protected function getRealizedRelation(): string
    {
        return UserSubjectRelation::FRIEND;
    }
}