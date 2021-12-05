<?php

namespace App\Event\Handler\Comment;

use App\Event\Handler\EventHandler;
use App\Event\Comment\ReportCommentEvent;
use App\Message\Comment\NotifyAdminsAboutCommentReportCommand;
use App\Message\Comment\LogCommentReportRequestCommand;
use Symfony\Component\Messenger\MessageBusInterface;

class ReportCommentEventHandler implements EventHandler
{
	public function __construct(private MessageBusInterface $commandBus)
	{
	}

	public function __invoke(ReportCommentEvent $event): void
	{
		$this->commandBus->dispatch(
			new NotifyAdminsAboutCommentReportCommand(
				$event->getUserId(), 
				$event->getCommentId()
			)
		);
		$this->commandBus->dispatch(
			new LogCommentReportRequestCommand(
				$event->getUserId(), 
				$event->getCommentId()
			)
		);
	}
}