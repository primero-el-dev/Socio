<?php

namespace App\Controller\User;

use ApiPlatform\Core\Api\IriConverterInterface;
use App\Entity\User;
use App\Entity\UserSubjectRelation;
use App\Event\User\Relation\BreakFriendshipEvent;
use App\Repository\Interface\UserRepositoryInterface;
use App\Repository\Interface\UserSubjectRelationRepositoryInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class BreakFriendshipController extends AbstractController
{
    public function __construct(
        private MessageBusInterface $eventBus,
        private TranslatorInterface $translator,
        private IriConverterInterface $iriConverter,
        private UserRepositoryInterface $userRepository,
        private UserSubjectRelationRepositoryInterface $relationRepository
    ) {
    }

    #[ParamConverter('id', class: User::class)]
    public function __invoke(User $user, Request $request): Response
    {
        $loggedUser = $this->userRepository->find($this->getUser()->getId());

        $this->deleteFriendship($loggedUser, $user);

        $this->eventBus->dispatch(
            new BreakFriendshipEvent(
                $loggedUser->getId(),
                $user->getId()
            )
        );

        return $this->json([
            'success' => $this->translator->trans(
                'notification.success.friendshipBroken'
            ),
        ]);
    }

    private function deleteFriendship(User $first, User $second): void
    {
        $this->relationRepository->deleteWhere(
            $first->getId(), 
            UserSubjectRelation::FRIEND, 
            $this->iriConverter->getIriFromItem($second)
        );
        $this->relationRepository->deleteWhere(
            $second->getId(), 
            UserSubjectRelation::FRIEND, 
            $this->iriConverter->getIriFromItem($first)
        );
    }
}
