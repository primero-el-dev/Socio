<?php

namespace App\Controller\User;

use ApiPlatform\Core\Api\IriConverterInterface;
use App\Entity\User;
use App\Entity\UserSubjectRelation;
use App\Event\User\RequestFriendEvent;
use App\Repository\Interface\UserRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class RequestFriendController extends AbstractController
{
    public function __construct(
        private MessageBusInterface $eventBus,
        private TranslatorInterface $translator,
        private EntityManagerInterface $entityManager,
        private IriConverterInterface $iriConverter,
        private UserRepositoryInterface $userRepository
    ) {
    }

    #[ParamConverter('id', class: User::class)]
    public function __invoke(User $user, Request $request): Response
    {
        $loggedUser = $this->userRepository->find($this->getUser()->getId());
        $this->createRequestRelation($loggedUser, UserSubjectRelation::REQUEST_FRIEND, $user);

        $this->eventBus->dispatch(
            new RequestFriendEvent(
                $this->getUser()->getId(),
                $user->getId()
            )
        );

        return $this->json([
            'success' => $this->translator->trans(
                'notification.success.friendRequestSend'
            ),
        ]);
    }

    private function createRequestRelation(User $user, string $action, User $subject): void
    {
        $relation = new UserSubjectRelation();
        $relation->setWho($user);
        $relation->setAction($action);
        $relation->setSubjectIri($this->iriConverter->getIriFromItem($subject));

        $this->entityManager->persist($relation);
        $this->entityManager->flush();
    }
}
