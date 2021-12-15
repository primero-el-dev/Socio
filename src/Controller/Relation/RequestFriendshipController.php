<?php

namespace App\Controller\Relation;

use App\Controller\Relation\MakeUserUserRelationController;
use App\Entity\UserSubjectRelation;
use App\Event\User\Relation\RequestFriendshipEvent;

class RequestFriendshipController extends MakeUserUserRelationController
{
    protected function getEventClass(): string
    {
        return RequestFriendshipEvent::class;
    }

    protected function getLoggedUserCreateRelations(): array
    {
        return [
            UserSubjectRelation::REQUEST_FRIENDSHIP,
        ];
    }

    protected function getSubjectUserCreateRelations(): array
    {
        return [];
    }

    protected function getLoggedUserDeleteRelations(): array
    {
        return [];
    }

    protected function getSubjectUserDeleteRelations(): array
    {
        return [];
    }
    
    protected function getResponseKey(): string
    {
        return 'notification.success.friendshipRequested';
    }
}
