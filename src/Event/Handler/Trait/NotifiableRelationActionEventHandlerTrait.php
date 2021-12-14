<?php

namespace App\Event\Handler\Trait;

use App\Entity\User;
use App\Event\Handler\EventHandler;
use App\Event\Interface\NotifiableRelationActionEvent;
use App\Message\User\SendAppNotificationCommand;
use App\Repository\Interface\UserRepositoryInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

trait NotifiableRelationActionEventHandlerTrait
{
	protected UserRepositoryInterface $userRepository;
	protected TranslatorInterface $translator;
	protected string $subject;
	protected string $content;

	public function handleNotifiableRelationActionEvent(NotifiableRelationActionEvent $event): void
	{
		$user = $this->userRepository->find($event->getInitiatorId());

		$this->commandBus->dispatch(
			new SendAppNotificationCommand(
				[$event->getSubjectId()],
				$this->getSubject($user),
				$this->getContent($user)
			)
		);
	}

	protected function getSubject(User $user): string
	{
		return sprintf($this->translator->trans($this->subject), $user);
	}

	protected function getContent(User $user): string
	{
		return sprintf($this->translator->trans($this->content), $user);
	}
}