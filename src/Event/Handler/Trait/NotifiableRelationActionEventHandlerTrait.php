<?php

namespace App\Event\Handler\Trait;

use App\Entity\User;
use App\Event\Handler\EventHandler;
use App\Event\Interface\NotifiableRelationActionEvent;
use App\Message\User\SendAppNotificationCommand;
use App\Repository\Interface\UserRepositoryInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use ApiPlatform\Core\Api\IriConverterInterface;

trait NotifiableRelationActionEventHandlerTrait
{
	protected UserRepositoryInterface $userRepository;
	protected TranslatorInterface $translator;
	protected IriConverterInterface $iriConverter;
	protected string $subject;
	protected string $content;

	public function handleNotifiableRelationActionEvent(
		NotifiableRelationActionEvent $event
	): void
	{
		$user = $this->userRepository->find($event->getInitiatorId());

		$this->commandBus->dispatch(
			new SendAppNotificationCommand(
				userIds: [$event->getSubjectId()],
				type: $event->getType(),
				subjectIri: $this->iriConverter->getIriFromItem($user),
				messageSubject: $this->getSubject($user),
				content: $this->getContent($user)
			)
		);
	}

	public function getSubject(User $user): string
	{
		return sprintf($this->translator->trans($this->getSubjectKey()), $user);
	}

	public function getContent(User $user): string
	{
		return sprintf($this->translator->trans($this->getContentKey()), $user);
	}
}