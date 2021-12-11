<?php

namespace App\Event\Handler\User;

use ApiPlatform\Core\Api\IriConverterInterface;
use App\Entity\Notification;
use App\Entity\User;
use App\Event\Handler\EventHandler;
use App\Event\User\RequestFriendEvent;
use App\Message\User\SendAppNotificationCommand;
use App\Repository\Interface\UserRepositoryInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class RequestFriendEventHandler implements EventHandler
{
	public function __construct(
		private MessageBusInterface $commandBus,
		private TranslatorInterface $translator,
		private UserRepositoryInterface $userRepository,
		private IriConverterInterface $iriConverter
	) {
	}

	public function __invoke(RequestFriendEvent $event)
	{
		$user = $this->userRepository->find($event->getUserId());

		$this->commandBus->dispatch(
			new SendAppNotificationCommand(
				userIds: [$event->getReceiverId()],
				type: Notification::FRIEND_REQUEST,
				subjectIri: $this->iriConverter->getIriFromItem($user),
				messageSubject: $this->translator->trans(
					'notification.info.friendRequestedSend.subject'),
				content: $this->getContent($user)
			)
		);
	}

	private function getContent(User $user)
	{
		return sprintf(
			$this->translator->trans(
				'notification.info.friendRequest.content'),
			$user->getTextIdentificator()
		);
	}
}
