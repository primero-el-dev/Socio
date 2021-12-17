<?php

namespace App\Event\User\Relation;

use App\Entity\Notification;
use App\Event\Interface\NotifiableRelationActionEvent;
use App\Event\User\Relation\RelationActionEvent;

class RequestFriendRelationEvent extends RelationActionEvent implements NotifiableRelationActionEvent
{
    public function getType(): string
    {
        return Notification::REQUEST_FRIEND_RELATION;
    }
}