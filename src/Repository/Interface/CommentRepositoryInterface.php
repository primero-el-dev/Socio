<?php

namespace App\Repository\Interface;

use Doctrine\Persistence\ObjectRepository;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;

interface CommentRepositoryInterface extends ObjectRepository
{
	public function getCommentsForParent(Comment $parent): array;
}