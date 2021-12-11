<?php

namespace App\Repository\Interface;

use App\Entity\Token;
use App\Entity\User;
use Doctrine\Persistence\ObjectRepository;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;

interface TokenRepositoryInterface extends ObjectRepository
{
	public function getTokenByTypeAndValue(string $type, string $value): ?Token;

    public function getTokenByTypeValueUser(string $type, string $value, User $user): ?Token;

    public function isValid(string $type, string $value): bool;

    public function deleteByTypeAndUserId(string $type, int $userId): void;
}