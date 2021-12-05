<?php

namespace App\Message\Comment;

use App\Message\Command;
use App\Messege\Transport\AsyncDoctrine;

class LogCommentReportRequestCommand implements Command//, AsyncDoctrine
{
	public function __construct(
		private int $userId,
		private int $commentId
	) {
	}

	public function getUserId(): int
	{
		return $this->userId;
	}

	public function getCommentId(): int
	{
		return $this->commentId;
	}
}