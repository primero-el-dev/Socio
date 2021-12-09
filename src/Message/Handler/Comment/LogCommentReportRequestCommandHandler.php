<?php

namespace App\Message\Handler\Comment;

use ApiPlatform\Core\Api\IriConverterInterface;
use App\Entity\UserSubjectRelation;
use App\Message\Comment\LogCommentReportRequestCommand;
use App\Message\Handler\CommandHandler;
use App\Repository\Interface\CommentRepositoryInterface;
use App\Repository\Interface\UserRepositoryInterface;
use App\Repository\Interface\UserSubjectRelationRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class LogCommentReportRequestCommandHandler implements CommandHandler
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

	public function __invoke(LogCommentReportRequestCommand $command): void
	{
		$user = $this->userRepository->find($command->getUserId());
		$comment = $this->commentRepository->find($command->getCommentId());
		$iri = $this->iriConverter->getIriFromItem($comment);
		
		if ($this->relationRepository->isObjectIriReportedByUser($iri, $user)) {
			return;
		}

		$relation = new UserSubjectRelation();
		$relation->setAction('REPORT');
		$relation->setWho($user);
		$relation->setSubjectIri($iri);
		$relation->setDenied(false);
		$this->entityManager->persist($relation);
		$this->entityManager->flush();
	}
}