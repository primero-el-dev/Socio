<?php

namespace App\Event\Handler\User;

use App\Event\Handler\EventHandler;
use App\Event\User\RegistrationEvent;
use App\Message\User\SendRegistrationVerificationEmailCommand;
use Symfony\Component\Messenger\MessageBusInterface;

class RegistrationEventHandler implements EventHandler
{
	public function __construct(private MessageBusInterface $commandBus)
	{
	}

	public function __invoke(RegistrationEvent $event)
	{
		$this->commandBus->dispatch(
			new SendRegistrationVerificationEmailCommand($event->getUserId())
		);
	}
}