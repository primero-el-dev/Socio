<?php

namespace App\Event\User;

use App\Event\Event;

class RequestFriendEvent implements Event
{
	public function __construct(
		private int $userId,
		private int $receiverId,
	) {
	}

	public function getUserId(): int
	{
		return $this->userId;
	}

	public function getReceiverId(): int
	{
		return $this->receiverId;
	}
}