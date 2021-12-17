<?php

namespace App\Event\Handler\User\Relation;

use ApiPlatform\Core\Api\IriConverterInterface;
use App\Event\Handler\EventHandler;
use App\Event\Handler\Interface\NotifiableRelationActionEventHandlerInterface;
use App\Event\Handler\Trait\NotifiableRelationActionEventHandlerTrait;
use App\Event\User\Relation\AcceptFriendshipEvent;
use App\Repository\Interface\UserRepositoryInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

abstract class UserUserRelationEventHandler implements EventHandler
{
	use NotifiableRelationActionEventHandlerTrait;

	public function __construct(
		protected UserRepositoryInterface $userRepository,
		protected TranslatorInterface $translator,
		protected IriConverterInterface $iriConverter,
		protected MessageBusInterface $commandBus
	) {
	}
}