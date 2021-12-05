<?php

namespace App\Event\Handler\User;

use App\Event\Handler\EventHandler;
use App\Event\User\ResetPasswordRequestEvent;
use App\Message\User\SendResetPasswordEmailCommand;
use Symfony\Component\Messenger\MessageBusInterface;

class ResetPasswordRequestEventHandler implements EventHandler
{
	public function __construct(private MessageBusInterface $commandBus)
	{
	}

	public function __invoke(ResetPasswordRequestEvent $event)
	{
		$this->commandBus->dispatch(
			new SendResetPasswordEmailCommand($event->getUserId())
		);
	}
}