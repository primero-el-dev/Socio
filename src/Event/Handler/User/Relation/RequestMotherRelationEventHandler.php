<?php

namespace App\Event\Handler\User\Relation;

use App\Event\Handler\Interface\NotifiableRelationActionEventHandlerInterface;
use App\Event\Handler\Trait\NotifiableRelationActionEventHandlerTrait;
use App\Event\Handler\User\Relation\UserUserRelationEventHandler;
use App\Event\User\Relation\RequestMotherRelationEvent;

class RequestMotherRelationEventHandler extends UserUserRelationEventHandler implements NotifiableRelationActionEventHandlerInterface
{
    use NotifiableRelationActionEventHandlerTrait;

    public function __invoke(RequestMotherRelationEvent $event): void
    {
        $this->handleNotifiableRelationActionEvent($event);
    }

    public function getSubjectKey(): string
    {
        return 'notification.info.requestMother.subject';
    }

    public function getContentKey(): string
    {
        return 'notification.info.requestMother.content';
    }
}