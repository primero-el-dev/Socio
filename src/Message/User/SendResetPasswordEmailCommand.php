<?php

namespace App\Message\User;

use App\Message\Command;
use App\Message\Transport\AsyncDoctrine;

class SendResetPasswordEmailCommand implements Command, AsyncDoctrine
{
	public function __construct(private int $userId)
	{
	}

	public function getUserId(): int
	{
		return $this->userId;
	}
}