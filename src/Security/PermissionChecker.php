<?php

namespace App\Security;

use App\Repository\Interface\UserRepositoryInterface;
use App\Repository\Interface\UserSubjectRelationRepositoryInterface;

class PermissionChecker
{
	public function __construct(
		private UserRepositoryInterface $userRepository,
		private UserSubjectRelationRepositoryInterface $relationRepository
	) {
	}

	public function userCanSeeTimelineComment()
	{
		
	}
}