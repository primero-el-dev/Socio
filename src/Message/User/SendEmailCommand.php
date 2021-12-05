<?php

namespace App\Message\User;

use App\Message\Command;
use App\Message\Transport\AsyncDoctrine;

class SendEmailCommand implements Command//, AsyncDoctrine
{
	public function __construct(
		private int $userId,
		private string $subject,
		private string $content = ''
	) {
	}

	public function getUserIds(): int
	{
		return $this->userIds;
	}

	public function getSubject(): string
	{
		return $this->subject;
	}

	public function getContent(): string
	{
		return $this->content;
	}
}