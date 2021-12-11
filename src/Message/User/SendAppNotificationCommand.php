<?php

namespace App\Message\User;

use App\Message\Command;
use App\Message\Transport\AsyncDoctrine;

class SendAppNotificationCommand implements Command//, AsyncDoctrine
{
	public function __construct(
		private array $userIds,
		private string $type,
		private string $subjectIri,
		private string $messageSubject,
		private string $content = ''
	) {
	}

	public function getUserIds(): array
	{
		return $this->userIds;
	}

	public function getType(): string
	{
		return $this->type;
	}

	public function getSubjectIri(): string
	{
		return $this->subjectIri;
	}

	public function getMessageSubject(): string
	{
		return $this->messageSubject;
	}

	public function getContent(): string
	{
		return $this->content;
	}
}