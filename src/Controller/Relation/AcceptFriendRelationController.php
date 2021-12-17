<?php

namespace App\Controller\Relation;

use App\Entity\User;
use App\Controller\Relation\MakeUserUserRelationController;
use App\Entity\UserSubjectRelation;
use Symfony\Component\HttpFoundation\Request;
use App\Event\User\Relation\AcceptFriendRelationEvent;

class AcceptFriendRelationController extends MakeUserUserRelationController
{
    protected function getEventClass(): string
    {
        return AcceptFriendRelationEvent::class;
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
            
        ];
    }

    protected function getSubjectUserDeleteRelations(): array
    {
        return [
            UserSubjectRelation::REQUEST_FRIEND,
        ];
    }
    
    protected function getResponseKey(): string
    {
        return 'notification.success.relation.acceptFriend';
    }

    protected function additionalAction(User $user, Request $request): void
    {
        //
    }
}
