<?php

namespace App\Message\Handler\Comment;

use App\Message\Comment\NotifyOwnerThatCommentWasApprovedCommand;
use App\Message\Handler\CommandHandler;
use App\Repository\Interface\CommentRepositoryInterface;
use App\Repository\Interface\UserRepositoryInterface;
use App\Repository\Interface\UserSubjectRelationRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use ApiPlatform\Core\Api\IriConverterInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Notifier\Notification\Notification;
use Symfony\Component\Notifier\NotifierInterface;
use Symfony\Component\Notifier\Recipient\Recipient;

class NotifyOwnerThatCommentWasApprovedCommandHandler implements CommandHandler
{
	public function __construct(
		private UserRepositoryInterface $userRepository,
		private CommentRepositoryInterface $commentRepository,
		private UserSubjectRelationRepositoryInterface $relationRepository,
		private TranslatorInterface $translator,
		private EntityManagerInterface $entityManager,
		private IriConverterInterface $iriConverter
	) {
	}

	public function __invoke(NotifyOwnerThatCommentWasApprovedCommand $command): void
	{
		$user = $this->userRepository->find($command->getUserId());
		$comment = $this->commentRepository->find($command->getCommentId());
		$iri = $this->iriConverter->getIriFromItem($comment);
		
		if ($this->relationRepository->isObjectIriReportedByUser($iri, $user)) {
			return;
		}

		$this->createUserReportOf($user, $iri);
		
		if ($group = $comment->getGroup()) {
			$admins = $this->relationRepository->getAdminsFor($group);
		}
		else {
			$admins = $this->userRepository->getAdmins();
		}

		$this->sendNotificationsToAdmins($admins);
	}

	private function createUserReportOf(User $user, string $iri): void
	{
		$relation = new UserSubjectRelation();
		$relation->setAction('REPORT');
		$relation->setWho($user);
		$relation->setSubjectIri($iri);
		$relation->setDenied(false);

		$this->entityManager->persist($relation);
		$this->entityManager->flush();
	}

	private function sendNotificationsToAdmins(array $admins): void
	{
		foreach ($admins as $admin) {
			
		}
	}
}