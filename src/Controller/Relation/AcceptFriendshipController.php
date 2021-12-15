<?php

namespace App\Controller\Relation;

use App\Controller\Relation\MakeUserUserRelationController;
use App\Entity\UserSubjectRelation;
use App\Event\User\Relation\AcceptFriendshipEvent;

class AcceptFriendshipController extends MakeUserUserRelationController
{
    protected function getEventClass(): string
    {
        return AcceptFriendshipEvent::class;
    }

    protected function getLoggedUserCreateRelations(): array
    {
        return [
            UserSubjectRelation::FRIEND,
        ];
    }

    protected function getSubjectUserCreateRelations(): array
    {
        return [
            UserSubjectRelation::FRIEND,
        ];
    }

    protected function getLoggedUserDeleteRelations(): array
    {
        return [
            UserSubjectRelation::REQUEST_FRIENDSHIP,
        ];
    }

    protected function getSubjectUserDeleteRelations(): array
    {
        return [
            UserSubjectRelation::REQUEST_FRIENDSHIP,
        ];
    }
    
    protected function getResponseKey(): string
    {
        return 'notification.success.friendshipAccepted';
    }
}
