<?php

namespace App\Event\Handler\User\Relation;

use App\Event\Handler\Interface\NotifiableRelationActionEventHandlerInterface;
use App\Event\Handler\Trait\NotifiableRelationActionEventHandlerTrait;
use App\Event\User\Relation\AcceptFriendRelationEvent;
use App\Event\Handler\User\Relation\UserUserRelationEventHandler;

class AcceptFriendRelationEventHandler extends UserUserRelationEventHandler implements NotifiableRelationActionEventHandlerInterface
{
    use NotifiableRelationActionEventHandlerTrait;

    public function __invoke(AcceptFriendRelationEvent $event): void
    {
        $this->handleNotifiableRelationActionEvent($event);
    }

    public function getSubjectKey(): string
    {
        return 'notification.info.relation.acceptFriend.subject';
    }

    public function getContentKey(): string
    {
        return 'notification.info.relation.acceptFriend.content';
    }
}