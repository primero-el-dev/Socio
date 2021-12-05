<?php

namespace App\Message\Handler\User;

use App\Message\Handler\CommandHandler;
use App\Message\User\SendNotificationCommand;
use App\Repository\UserRepository;
use Symfony\Component\Notifier\Notification\Notification;
use Symfony\Component\Notifier\NotifierInterface;
use Symfony\Component\Notifier\Recipient\Recipient;

class SendNotificationCommandHandler implements CommandHandler
{
	public function __construct(
		private UserRepository $userRepository,
		private NotifierInterface $notifier
	) {
	}

	public function __invoke(SendNotificationCommand $command): void
	{
		$users = $this->userRepository->findBy(['id' => $command->getUserIds()]);
		$notification = (new Notification($command->getSubject(), $command->getChannels()))
            ->content($command->getContent());
        $recipients = array_map(
        	fn($user) => new Recipient(
	            $user->getEmail(),
	            $user->getPhone()
	        ), 
	        $users
	    );

        foreach ($recipients as $recipient) {
        	$this->notifier->send($notification, $recipients);
        }
	}
}