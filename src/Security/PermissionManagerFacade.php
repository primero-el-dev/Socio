<?php

namespace App\Security;

use ApiPlatform\Core\Api\IriConverterInterface;
use App\Entity\Entity;
use App\Entity\Group;
use App\Entity\User;
use App\Entity\UserUserRelation;
use App\Repository\Interface\UserSubjectRelationRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;

class PermissionManagerFacade
{
	public function __construct(
		private EntityManagerInterface $entityManager,
		private IriConverterInterface $iriConverter,
		private UserSubjectRelationRepositoryInterface $relationRepository
	) {
	}

	public function grantOnGroupJoin(User $user, Group $group): void
	{
		$groupIri = $this->iriConverter->getIriFromItem($group);
		$this->removeRelation($user, UserSubjectRelation::REQUEST_MEMBERSHIP, $groupIri);
		$this->createRelation($user, UserSubjectRelation::ROLE_MEMBER, $groupIri);
		$this->createRelation($user, UserSubjectRelation::READ_COMMENT, $groupIri);
		$this->createRelation($user, UserSubjectRelation::CREATE_COMMENT, $groupIri);
		$this->createRelation($user, UserSubjectRelation::REACT_COMMENT, $groupIri);
		$this->createRelation($user, UserSubjectRelation::REPORT_COMMENT, $groupIri);
		$this->entityManager->flush();
	}

	public function removeGrantsOnGroupQuit(User $user, Group $group): void
	{
		$groupIri = $this->iriConverter->getIriFromItem($group);
		$this->removeRelation($user, UserSubjectRelation::ROLE_MEMBER, $groupIri);
		$this->removeRelation($user, UserSubjectRelation::READ_COMMENT, $groupIri);
		$this->removeRelation($user, UserSubjectRelation::CREATE_COMMENT, $groupIri);
		$this->removeRelation($user, UserSubjectRelation::REACT_COMMENT, $groupIri);
		$this->removeRelation($user, UserSubjectRelation::REPORT_COMMENT, $groupIri);
	}

	public function hasPermissionOnGroup(
		User $user, 
		string $permission, 
		Group $group
	): bool
	{
		
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