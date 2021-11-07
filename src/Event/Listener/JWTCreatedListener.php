<?php

namespace App\Event\Listener;

use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTCreatedEvent;

class JWTCreatedListener
{
	public function onJWTCreated(JWTCreatedEvent &$event)
    {
        $user = $event->getUser();
        $payload = array_merge(
            $event->getData(),
            ['userId' => $user->getId()]
        );

        $event->setData($payload);
    }
}