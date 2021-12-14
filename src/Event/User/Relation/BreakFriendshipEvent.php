<?php

namespace App\Event\User\Relation;

use App\Event\Interface\NotifiableRelationActionEvent;
use App\Event\User\Relation\RelationActionEvent;

class BreakFriendshipEvent extends RelationActionEvent implements NotifiableRelationActionEvent
{

}