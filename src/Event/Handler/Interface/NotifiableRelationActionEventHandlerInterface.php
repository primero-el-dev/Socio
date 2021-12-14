<?php

namespace App\Event\Handler\Interface;

use App\Event\Handler\EventHandler;
use App\Event\Interface\NotifiableRelationActionEvent;

interface NotifiableRelationActionEventHandlerInterface extends EventHandler
{
	public function handleNotifiableRelationActionEvent(NotifiableRelationActionEvent $event): void;
}