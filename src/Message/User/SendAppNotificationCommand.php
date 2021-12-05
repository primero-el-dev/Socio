<?php

namespace App\Message\User;

use App\Message\Command;
use App\Message\Transport\AsyncDoctrine;

class SendAppNotificationCommand implements Command//, AsyncDoctrine
{
	public function __construct(
		private array $userIds,
		private string $subject,
		private string $content = ''
	) {
	}

	public function getUserIds(): array
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