<?php

namespace App\Event\Comment;

use App\Event\Event;

class BanCommentEvent implements Event
{
	public function __construct(
		private int $commentId
	) {
	}

	public function getCommentId(): int
	{
		return $this->commentId;
	}
}
