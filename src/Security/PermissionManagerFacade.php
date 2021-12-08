<?php

namespace App\Security;

use App\Entity\Entity;
use App\Entity\Group;
use App\Entity\User;
use App\Entity\UserUserRelation;
use App\Repository\UserSubjectRelationRepository;
use ApiPlatform\Core\Api\IriConverterInterface;
use Doctrine\ORM\EntityManagerInterface;

class PermissionManagerFacade
{
	public function __construct(
		private EntityManagerInterface $entityManager,
		private IriConverterInterface $iriConverter,
		private UserSubjectRelationRepository $relationRepository
	) {
	}

	public function grantOnGroupJoin(User $user, Group $group): void
	{
		$groupIri = $this->iriConverter->getIriFromItem($group);
		
		$this->removeRelation($user, 'REQUEST_MEMBERSHIP', $groupIri);
		$this->createRelation($user, 'ROLE_MEMBER', $groupIri);
		$this->createRelation($user, 'READ_COMMENT', $groupIri);
		$this->createRelation($user, 'CREATE_COMMENT', $groupIri);
		$this->createRelation($user, 'REACT_COMMENT', $groupIri);
		$this->createRelation($user, 'REPORT_COMMENT', $groupIri);

		$this->entityManager->flush();
	}

	public function removeGrantsOnExitGroup(User $user, Group $group): void
	{
		$groupIri = $this->iriConverter->getIriFromItem($group);
		
		$this->removeRelation($user, 'ROLE_MEMBER', $groupIri);
		$this->removeRelation($user, 'READ_COMMENT', $groupIri);
		$this->removeRelation($user, 'CREATE_COMMENT', $groupIri);
		$this->removeRelation($user, 'REACT_COMMENT', $groupIri);
		$this->removeRelation($user, 'REPORT_COMMENT', $groupIri);
	}

	private function createRelation(
		User $user, 
		string $action, 
		string $subjectIri,
		?bool $denied = null,
		?\DateTimeInterface $terminatedAt = null
	): UserUserRelation
	{
		$relation = new UserSubjectRelation();
		$relation->setWho($user);
		$relation->setSubjectIri($subjectIri);
		$relation->setAction($action);
		$relation->setDenied($denied);
		$relation->setTerminatedAt($terminatedAt);
		$this->entityManager->persist($relation);

		return $relation;
	}

	private function removeRelation(
		User $user, 
		string $action, 
		string $subjectIri
	): void
	{
		$this->relationRepository->removeWhere($user->getId(), $action, $subjectIri);
	}
}