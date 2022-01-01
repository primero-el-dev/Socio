<?php

namespace App\Event\Handler\User\Relation;

use App\Event\Handler\Interface\NotifiableRelationActionEventHandlerInterface;
use App\Event\Handler\Trait\NotifiableRelationActionEventHandlerTrait;
use App\Event\User\Relation\AcceptFatherRelationEvent;
use App\Event\Handler\User\Relation\UserUserRelationEventHandler;

class AcceptFatherRelationEventHandler extends UserUserRelationEventHandler implements NotifiableRelationActionEventHandlerInterface
{
    use NotifiableRelationActionEventHandlerTrait;

    public function __invoke(AcceptFatherRelationEvent $event): void
    {
        $this->handleNotifiableRelationActionEvent($event);
    }

    public function getSubjectKey(): string
    {
        return 'notification.info.relation.acceptFather.subject';
    }

    public function getContentKey(): string
    {
        return 'notification.info.relation.acceptFather.content';
    }
}