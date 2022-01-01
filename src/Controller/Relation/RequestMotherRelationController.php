<?php

namespace App\Controller\Relation;

use App\Entity\User;
use App\Controller\Relation\MakeUserUserRelationController;
use App\Entity\UserSubjectRelation;
use Symfony\Component\HttpFoundation\Request;
use App\Event\User\Relation\RequestMotherRelationEvent;

class RequestMotherRelationController extends MakeUserUserRelationController
{
    protected function getEventClass(): string
    {
        return RequestMotherRelationEvent::class;
    }

    protected function getLoggedUserCreateRelations(User $user, User $subject): array
    {
        return [
            UserSubjectRelation::REQUEST_MOTHER,
        ];
    }

    protected function getSubjectUserCreateRelations(User $user, User $subject): array
    {
        return [
            
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
            
        ];
    }
    
    protected function getResponseKey(): string
    {
        return 'notification.success.relation.requestMother';
    }

    protected function additionalAction(User $user, Request $request): void
    {
        //
    }
}
