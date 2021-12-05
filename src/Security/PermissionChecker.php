<?php

namespace App\Security;

use App\Repository\UserRepository;
use App\Repository\UserSubjectRelationRepository;

class PermissionChecker
{
	public function __construct(
		private UserRepository $userRepository,
		private UserSubjectRelationRepository $relationRepository
	) {
	}

	public function userCanSeeTimelineComment()
	{
		
	}
}