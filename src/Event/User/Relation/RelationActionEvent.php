<?php

namespace App\Event\User\Relation;

use App\Event\Event;

abstract class RelationActionEvent implements Event
{
	public function __construct(
		int $initiatorId, 
		int $subjectId
	) {
	}

	public function getInitiatorId(): int
	{
		return $this->initiatorId;
	}

	public function getSubjectId(): int
	{
		return $this->subjectId;
	}
}