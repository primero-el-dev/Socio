<?php

namespace App\Controller\Relation;

use App\Entity\User;
use App\Entity\UserSubjectRelation;
use App\Controller\Relation\BreakUserUserRelationController;
use Symfony\Component\HttpFoundation\Request;
use App\Event\User\Relation\BreakMotherRelationEvent;

class BreakFriendRelationController extends BreakUserUserRelationController
{
    protected function getEventClass(): string
    {
        return BreakFriendRelationEvent::class;
    }

    protected function getLoggedUserDeleteRelations(): array
    {
        return [
            UserSubjectRelation::FRIEND,
        ];
    }

    protected function getSubjectUserDeleteRelations(): array
    {
        return [
            UserSubjectRelation::FRIEND,
        ];
    }
    
    protected function getResponseKey(): string
    {
        return 'notification.success.relation.breakFriend';
    }

    protected function additionalAction(User $user, Request $request): void
    {
        //
    }
}
