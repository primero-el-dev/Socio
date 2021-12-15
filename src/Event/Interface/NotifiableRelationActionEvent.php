<?php

namespace App\Event\Interface;

interface NotifiableRelationActionEvent
{
	public function getInitiatorId(): int;

	public function getSubjectId(): int;

	public function getType(): string;
}