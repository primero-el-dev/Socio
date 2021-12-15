<?php

namespace App\Controller\Relation;

use App\Entity\UserSubjectRelation;
use App\Controller\Relation\BreakUserUserRelationController;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use App\Event\User\Relation\BreakFriendshipEvent;

class BreakFriendshipController extends BreakUserUserRelationController
{
    protected function getEventClass(): string
    {
        return BreakFriendshipEvent::class;
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
        return 'notification.success.friendshipBroken';
    }
}
