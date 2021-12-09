<?php

namespace App\Repository\Interface;

use Doctrine\Persistence\ObjectRepository;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;

interface UserRepositoryInterface extends ObjectRepository
{
	public function upgradePassword(
		PasswordAuthenticatedUserInterface $user, 
		string $newHashedPassword
	): void;

	public function getAdmins(): array;

    public function findByRole(string $role): array;
}