<?php

namespace App\Message\Comment;

use App\Message\Command;
use App\Messege\Transport\AsyncDoctrine;

class NotifyOwnerThatCommentWasApprovedCommand implements Command//, AsyncDoctrine
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