<?php

namespace App\Message\Handler\User;

use App\Entity\Notification;
use App\Entity\User;
use App\Message\Handler\CommandHandler;
use App\Message\User\SendAppNotificationCommand;
use App\Repository\Interface\UserRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;

class SendAppNotificationCommandHandler implements CommandHandler
{
	public function __construct(
		private UserRepositoryInterface $userRepository,
		private EntityManagerInterface $entityManager
	) {
	}

	public function __invoke(SendAppNotificationCommand $command): void
	{
		$users = $this->userRepository->findBy(['id' => $command->getUserIds()]);

		for ($i = 0; $i < count($users); $i++) {
			$notification = $this->createNotification($command, $users[$i]);
			$this->entityManager->persist($notification);
			
			if (($i !== 0) && ($i % 100 === 0)) {
				$this->entityManager->flush();
			}
		}
		$this->entityManager->flush();
	}

	private function createNotification(
		SendAppNotificationCommand $command,
		User $user
	): notification
	{
		$notification = new Notification();
		$notification->setType($command->getType());
		$notification->setSubjectIri($command->getSubjectIri());
		$notification->setMessage($command->getContent());
		$notification->setMessageSubject($command->getMessageSubject());

		return $notification;
	}
}