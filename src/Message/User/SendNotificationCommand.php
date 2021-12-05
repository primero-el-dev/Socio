<?php

namespace App\Message\User;

use App\Message\Command;
use App\Message\Transport\AsyncDoctrine;

class SendNotificationCommand implements Command//, AsyncDoctrine
{
	public function __construct(
		private array $userIds,
		private string $subject,
		private string $content = '',
		private array $channels = []
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

	public function getChannels(): array
	{
		return $this->channels;
	}
}