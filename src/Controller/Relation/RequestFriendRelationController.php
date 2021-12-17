<?php

namespace App\Controller\Relation;

use App\Entity\User;
use App\Controller\Relation\MakeUserUserRelationController;
use App\Entity\UserSubjectRelation;
use Symfony\Component\HttpFoundation\Request;
use App\Event\User\Relation\RequestFriendRelationEvent;

class RequestFriendRelationController extends MakeUserUserRelationController
{
    protected function getEventClass(): string
    {
        return RequestFriendRelationEvent::class;
    }

    protected function getLoggedUserCreateRelations(): array
    {
        return [
            UserSubjectRelation::REQUEST_FRIEND,
        ];
    }

    protected function getSubjectUserCreateRelations(): array
    {
        return [
            
        ];
    }

    protected function getLoggedUserDeleteRelations(): array
    {
        return [
            
        ];
    }

    protected function getSubjectUserDeleteRelations(): array
    {
        return [
            
        ];
    }
    
    protected function getResponseKey(): string
    {
        return 'notification.success.relation.requestFriend';
    }

    protected function additionalAction(User $user, Request $request): void
    {
        //
    }
}
