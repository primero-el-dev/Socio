<?php

namespace App\Message\Handler\Comment;

use App\Entity\UserSubjectRelation;
use App\Message\Comment\LogCommentReportRequestCommand;
use App\Message\Handler\CommandHandler;
use App\Repository\CommentRepository;
use App\Repository\UserRepository;
use App\Repository\UserSubjectRelationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use ApiPlatform\Core\Api\IriConverterInterface;

class LogCommentReportRequestCommandHandler implements CommandHandler
{
	public function __construct(
		private UserRepository $userRepository,
		private CommentRepository $commentRepository,
		private UserSubjectRelationRepository $relationRepository,
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