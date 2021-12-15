<?php

namespace App\Event\User\Relation;

use App\Event\Event;

abstract class RelationActionEvent implements Event
{
	public function __construct(
		protected int $initiatorId, 
		protected int $subjectId
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

	abstract public function getType(): string;
}