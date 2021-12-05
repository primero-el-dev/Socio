<?php

namespace App\Event\Handler\Comment;

use App\Event\Comment\ApproveCommentEvent;
use App\Event\Handler\EventHandler;
use App\Message\Comment\NotifyOwnerThatCommentWasApprovedCommand;
use Symfony\Component\Messenger\MessageBusInterface;

class ApproveCommentEventHandler implements EventHandler
{
	public function __construct(private MessageBusInterface $commandBus)
	{
	}

	public function __invoke(ApproveCommentEvent $event): void
	{
		$this->commandBus->dispatch(
			new NotifyOwnerThatCommentWasApprovedCommand(
				$event->getCommentId()
			)
		);
	}
}