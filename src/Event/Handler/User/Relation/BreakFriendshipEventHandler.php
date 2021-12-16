<?php

namespace App\Event\Handler\User\Relation;

use App\Event\Handler\Interface\NotifiableRelationActionEventHandlerInterface;
use App\Event\Handler\Trait\NotifiableRelationActionEventHandlerTrait;
use App\Event\User\Relation\BreakFriendshipEvent;

class BreakFriendshipEventHandler implements NotifiableRelationActionEventHandlerInterface
{
	use NotifiableRelationActionEventHandlerTrait;

	public function __invoke(BreakFriendshipEvent $event): void
	{
		$this->handleNotifiableRelationActionEvent($event);
	}

	public function getSubjectKey(): string
	{
		return 'notification.info.breakFriendship.subject';
	}

	public function getContentKey(): string
	{
		return 'notification.info.breakFriendship.content';
	}
}