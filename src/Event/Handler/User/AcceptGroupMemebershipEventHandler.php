<?php

namespace App\Event\Handler\User;

use App\Event\Handler\EventHandler;
use App\Event\User\RequestGroupMembershipEvent;
use App\Message\User\SendAppNotificationCommand;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class AcceptGroupMemebershipEventHandler implements EventHandler
{
	public function __construct(
		private MessageBusInterface $commandBus,
		private TranslatorInterface $translator
	) {
	}

	public function __invoke(RequestGroupMembershipEvent $event)
	{
		$this->commandBus->dispatch(
			new SendAppNotificationCommand(
				[$event->getUserId()],
				$this->translator->trans(
					'notification.info.groupMembershipAccepted.subject'),
				$this->getContent($event->getGroupName())
			)
		);
	}

	private function getContent(string $groupName)
	{
		return sprintf(
			$this->translator->trans(
				'notification.info.groupMembershipAccepted.content'),
			$groupName
		);
	}
}