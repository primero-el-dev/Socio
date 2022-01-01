<?php

namespace App\Controller\Relation;

use App\Entity\User;
use App\Controller\Relation\MakeUserUserRelationController;
use App\Entity\UserSubjectRelation;
use Symfony\Component\HttpFoundation\Request;
use App\Event\User\Relation\AcceptMotherRelationEvent;

class AcceptMotherRelationController extends MakeUserUserRelationController
{
    protected function getEventClass(): string
    {
        return AcceptMotherRelationEvent::class;
    }

    protected function getLoggedUserCreateRelations(User $user, User $subject): array
    {
        return [
            
        ];
    }

    protected function getSubjectUserCreateRelations(User $user, User $subject): array
    {
        return [
            UserSubjectRelation::MOTHER,
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
            UserSubjectRelation::REQUEST_MOTHER,
        ];
    }
    
    protected function getResponseKey(): string
    {
        return 'notification.success.relation.acceptMother';
    }

    protected function additionalAction(User $user, Request $request): void
    {
        //
    }
}
