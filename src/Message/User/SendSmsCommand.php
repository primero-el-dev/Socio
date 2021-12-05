<?php

namespace App\Message\User;

use App\Message\Command;
use App\Message\Transport\AsyncDoctrine;

class SendSmsCommand implements Command//, AsyncDoctrine
{
	public function __construct(
		private int $userId,
		private string $subject,
		private string $content = ''
	) {
	}

	public function getUserId(): int
	{
		return $this->userId;
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