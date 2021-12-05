<?php

namespace App\Message\Handler\User;

use App\Message\Handler\CommandHandler;
use App\Message\User\SendSmsCommand;
use App\Repository\UserRepository;
use Symfony\Component\Notifier\Notification\Notification;
use Symfony\Component\Notifier\NotifierInterface;
use Symfony\Component\Notifier\Recipient\Recipient;

class SendSmsCommandHandler implements CommandHandler
{
	public function __construct(
		private UserRepository $userRepository,
		private NotifierInterface $notifier
	) {
	}

	public function __invoke(SendSmsCommand $command): void
	{
		$user = $this->userRepository->find($command->getUserId());
		$notification = (new Notification($command->getSubject(), ['sms/sinch']))
            ->content($command->getContent());
        $recipient = new Recipient(
            $user->getEmail(),
            $user->getPhone()
        );

        $this->notifier->send($notification, $recipient);
	}
}