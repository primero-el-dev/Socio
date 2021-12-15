<?php

namespace App\Event\Handler\User\Relation;

use App\Event\Handler\Interface\NotifiableRelationActionEventHandlerInterface;
use App\Event\Handler\Trait\NotifiableRelationActionEventHandlerTrait;
use App\Event\User\Relation\RequestFriendshipEvent;

class RequestFriendshipEventHandler implements NotifiableRelationActionEventHandlerInterface
{
	use NotifiableRelationActionEventHandlerTrait;

	public function __invoke(RequestFriendshipEvent $event): void
	{
		$this->handleNotifiableRelationActionEvent($event);
	}

	public function getSubjectKey(): string
	{
		return 'notification.info.requestFriendship.subject';
	}

	public function getContentKey(): string
	{
		return 'notification.info.requestFriendship.content';
	}
}