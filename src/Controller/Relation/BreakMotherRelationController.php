<?php

namespace App\Controller\Relation;

use App\Entity\User;
use App\Entity\UserSubjectRelation;
use App\Controller\Relation\BreakUserUserRelationController;
use Symfony\Component\HttpFoundation\Request;
use App\Event\User\Relation\BreakMotherRelationEvent;

class BreakMotherRelationController extends BreakUserUserRelationController
{
    protected function getEventClass(): string
    {
        return BreakMotherRelationEvent::class;
    }

    protected function getLoggedUserDeleteRelations(User $user, User $subject): array
    {
        return [
            UserSubjectRelation::MOTHER,
        ];
    }

    protected function getSubjectUserDeleteRelations(User $user, User $subject): array
    {
        return [

        ];
    }
    
    protected function getResponseKey(): string
    {
        return 'notification.success.relation.breakMother';
    }

    protected function additionalAction(User $user, Request $request): void
    {
        //
    }
}
