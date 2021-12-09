<?php

namespace App\Repository\Interface;

use Doctrine\Persistence\ObjectRepository;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;

interface ReactionRepositoryInterface extends ObjectRepository
{
	public function deleteForUserAndComment(User $user, Comment $comment): void;

    public function getForUserAndComment(User $user, Comment $comment): ?Reaction;
}