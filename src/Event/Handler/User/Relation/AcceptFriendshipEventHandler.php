<?php

namespace App\Event\Handler\User\Relation;

use App\Event\Handler\Interface\NotifiableRelationActionEventHandlerInterface;
use App\Event\Handler\Trait\NotifiableRelationActionEventHandlerTrait;
use App\Event\User\Relation\AcceptFriendshipEvent;

class AcceptFriendshipEventHandler implements NotifiableRelationActionEventHandlerInterface
{
	use NotifiableRelationActionEventHandlerTrait;

	public function __invoke(AcceptFriendshipEvent $event): void
	{
		$this->handleNotifiableRelationActionEvent($event);
	}

	public function getSubjectKey(): string
	{
		return 'notification.info.acceptFriendship.subject';
	}

	public function getContentKey(): string
	{
		return 'notification.info.acceptFriendship.content';
	}
}