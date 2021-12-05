<?php

namespace App\Event\User;

use App\Event\Event;

class ResetPasswordRequestEvent implements Event
{
	public function __construct(private int $userId)
	{
	}

	public function getUserId(): int
	{
		return $this->userId;
	}
}