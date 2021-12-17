<?php

namespace App\Event\Handler\User\Relation;

use App\Event\Handler\Interface\NotifiableRelationActionEventHandlerInterface;
use App\Event\Handler\Trait\NotifiableRelationActionEventHandlerTrait;
use App\Event\User\Relation\BreakFriendRelationEvent;
use App\Event\Handler\User\Relation\UserUserRelationEventHandler;

class BreakFriendRelationEventHandler extends UserUserRelationEventHandler implements NotifiableRelationActionEventHandlerInterface
{
    use NotifiableRelationActionEventHandlerTrait;

    public function __invoke(BreakFriendRelationEvent $event): void
    {
        $this->handleNotifiableRelationActionEvent($event);
    }

    public function getSubjectKey(): string
    {
        return 'notification.info.relation.breakFriend.subject';
    }

    public function getContentKey(): string
    {
        return 'notification.info.relation.breakFriend.content';
    }
}