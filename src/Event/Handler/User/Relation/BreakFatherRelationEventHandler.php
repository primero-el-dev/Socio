<?php

namespace App\Event\Handler\User\Relation;

use App\Event\Handler\Interface\NotifiableRelationActionEventHandlerInterface;
use App\Event\Handler\Trait\NotifiableRelationActionEventHandlerTrait;
use App\Event\User\Relation\BreakFatherRelationEvent;
use App\Event\Handler\User\Relation\UserUserRelationEventHandler;

class BreakFatherRelationEventHandler extends UserUserRelationEventHandler implements NotifiableRelationActionEventHandlerInterface
{
    use NotifiableRelationActionEventHandlerTrait;

    public function __invoke(BreakFatherRelationEvent $event): void
    {
        $this->handleNotifiableRelationActionEvent($event);
    }

    public function getSubjectKey(): string
    {
        return 'notification.info.relation.breakFather.subject';
    }

    public function getContentKey(): string
    {
        return 'notification.info.relation.breakFather.content';
    }
}