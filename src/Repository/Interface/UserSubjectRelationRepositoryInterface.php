<?php

namespace App\Repository\Interface;

use Doctrine\Persistence\ObjectRepository;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;

interface UserSubjectRelationRepositoryInterface extends ObjectRepository
{
	public function findForSubject(Entity $subject): ?array;

    public function isObjectIriReportedByUser(string $iri, User $user): bool;

    public function userCanOn(
        User $user, 
        string $action, 
        ?Entity $subject,
        bool $default = true
    ): bool;

    public function userHasRelationWith(
        User $user, 
        string $action, 
        ?Entity $subject,
        bool $default = true
    ): bool;

    public function userCanOnSubjectIri(
        User $user, 
        string $action, 
        string $subjectIri,
        bool $default = true
    ): bool;

    public function deleteWhere(int $userId, string $action, string $subjectIri): void;

    public function getAdminsForIri(string $subjectIri): array;

    public function getAdminsFor(Entity $entity): array;
}