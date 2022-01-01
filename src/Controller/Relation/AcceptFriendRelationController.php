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

    protected function getLoggedUserCreateRelations(User $user, User $subject): array
    {
        return [
            UserSubjectRelation::FRIEND,
        ];
    }

    protected function getSubjectUserCreateRelations(User $user, User $subject): array
    {
        return [
            UserSubjectRelation::FRIEND,
        ];
    }

    protected function getLoggedUserDeleteRelations(User $user, User $subject): array
    {
        return [
            
        ];
    }

    protected function getSubjectUserDeleteRelations(User $user, User $subject): array
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
