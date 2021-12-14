<?php

namespace App\Event\Handler\User\Relation;

use App\Event\Handler\EventHandler;
use App\Event\Handler\Interface\NotifiableRelationActionEventHandlerInterface;
use App\Event\Handler\Trait\NotifiableRelationActionEventHandlerTrait;
use App\Event\Handler\User\Relation\NotifiableRelationActionEventHandler;
use App\Event\User\Relation\BreakFriendshipEvent;
use App\Repository\Interface\UserRepositoryInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class BreakFriendshipEventHandler implements NotifiableRelationActionEventHandlerInterface
{
	use NotifiableRelationActionEventHandlerTrait;

	public function __construct(
		protected MessageBusInterface $commandBus,
		protected TranslatorInterface $translator,
		protected UserRepositoryInterface $userRepository
	) {
		$this->subject = 'notification.info.breakFriendship.subject';
		$this->content = 'notification.info.breakFriendship.content';
	}

	public function __invoke(BreakFriendshipEvent $event): void
	{
		$this->handleNotifiableRelationActionEvent($event);
	}
}