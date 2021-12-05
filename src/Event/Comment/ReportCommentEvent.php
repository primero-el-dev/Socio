<?php

namespace App\Event\Comment;

use App\Event\Event;

class ReportCommentEvent implements Event
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