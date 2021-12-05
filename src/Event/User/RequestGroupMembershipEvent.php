<?php

namespace App\Event\User;

use App\Event\Event;

class RequestGroupMembershipEvent implements Event
{
	public function __construct(
		private int $userId,
		private string $groupName,
	) {
	}

	public function getUserId(): int
	{
		return $this->userId;
	}

	public function getGroupName(): string
	{
		return $this->groupName;
	}
}