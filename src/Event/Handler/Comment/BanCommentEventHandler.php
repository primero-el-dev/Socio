<?php

namespace App\Event\Handler\Comment;

use App\Event\Comment\BanCommentEvent;
use App\Event\Handler\EventHandler;
use App\Message\Comment\NotifyOwnerThatCommentWasBannedCommand;
use Symfony\Component\Messenger\MessageBusInterface;

class BanCommentEventHandler implements EventHandler
{
	public function __construct(private MessageBusInterface $commandBus)
	{
	}

	public function __invoke(BanCommentEvent $event): void
	{
		$this->commandBus->dispatch(
			new NotifyOwnerThatCommentWasBannedCommand(
				$event->getCommentId()
			)
		);
	}
}