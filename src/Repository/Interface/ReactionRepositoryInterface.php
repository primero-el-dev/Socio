<?php

namespace App\Repository\Interface;

use App\Entity\Comment;
use App\Entity\Reaction;
use App\Entity\User;
use Doctrine\Persistence\ObjectRepository;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;

interface ReactionRepositoryInterface extends ObjectRepository
{
	public function deleteForUserAndComment(User $user, Comment $comment): void;

    public function getForUserAndComment(User $user, Comment $comment): ?Reaction;
}