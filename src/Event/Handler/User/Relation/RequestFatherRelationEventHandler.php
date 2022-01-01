<?php

namespace App\Event\Handler\User\Relation;

use App\Event\Handler\Interface\NotifiableRelationActionEventHandlerInterface;
use App\Event\Handler\Trait\NotifiableRelationActionEventHandlerTrait;
use App\Event\User\Relation\RequestFatherRelationEvent;
use App\Event\Handler\User\Relation\UserUserRelationEventHandler;

class RequestFatherRelationEventHandler extends UserUserRelationEventHandler implements NotifiableRelationActionEventHandlerInterface
{
    use NotifiableRelationActionEventHandlerTrait;

    public function __invoke(RequestFatherRelationEvent $event): void
    {
        $this->handleNotifiableRelationActionEvent($event);
    }

    public function getSubjectKey(): string
    {
        return 'notification.info.relation.requestFather.subject';
    }

    public function getContentKey(): string
    {
        return 'notification.info.relation.requestFather.content';
    }
}