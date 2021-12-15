<?php

namespace App\Event\User\Relation;

use App\Entity\Notification;
use App\Event\Interface\NotifiableRelationActionEvent;
use App\Event\User\Relation\RelationActionEvent;

class AcceptFriendshipEvent extends RelationActionEvent implements NotifiableRelationActionEvent
{
	public function getType(): string
	{
		return Notification::ACCEPT_FRIENDSHIP;
	}
}