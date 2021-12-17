<?php

namespace App\Event\Handler\User\Relation;

use App\Event\Handler\Interface\NotifiableRelationActionEventHandlerInterface;
use App\Event\Handler\Trait\NotifiableRelationActionEventHandlerTrait;
use App\Event\User\Relation\RequestFriendRelationEvent;
use App\Event\Handler\User\Relation\UserUserRelationEventHandler;

class RequestFriendRelationEventHandler extends UserUserRelationEventHandler implements NotifiableRelationActionEventHandlerInterface
{
    use NotifiableRelationActionEventHandlerTrait;

    public function __invoke(RequestFriendRelationEvent $event): void
    {
        $this->handleNotifiableRelationActionEvent($event);
    }

    public function getSubjectKey(): string
    {
        return 'notification.info.relation.requestFriend.subject';
    }

    public function getContentKey(): string
    {
        return 'notification.info.relation.requestFriend.content';
    }
}