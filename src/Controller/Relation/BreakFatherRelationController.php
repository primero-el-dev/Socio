<?php

namespace App\Controller\Relation;

use App\Entity\User;
use App\Entity\UserSubjectRelation;
use App\Controller\Relation\BreakUserUserRelationController;
use Symfony\Component\HttpFoundation\Request;
use App\Event\User\Relation\BreakFatherRelationEvent;

class BreakFatherRelationController extends BreakUserUserRelationController
{
    protected function getEventClass(): string
    {
        return BreakFatherRelationEvent::class;
    }

    protected function getLoggedUserDeleteRelations(User $user, User $subject): array
    {
        return [
            UserSubjectRelation::FATHER,
        ];
    }

    protected function getSubjectUserDeleteRelations(User $user, User $subject): array
    {
        return [

        ];
    }
    
    protected function getResponseKey(): string
    {
        return 'notification.success.relation.breakFather';
    }

    protected function additionalAction(User $user, Request $request): void
    {
        //
    }
}
