<?php

namespace App\Controller\Relation;

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
use Doctrine\ORM\EntityManagerInterface;

abstract class BreakUserUserRelationController extends AbstractController
{
    public function __construct(
        protected MessageBusInterface $eventBus,
        protected TranslatorInterface $translator,
        protected IriConverterInterface $iriConverter,
        protected EntityManagerInterface $entityManagerPRe,
        protected UserRepositoryInterface $userRepository,
        protected UserSubjectRelationRepositoryInterface $relationRepository
    ) {
    }

    #[ParamConverter('id', class: User::class)]
    public function __invoke(User $user, Request $request): Response
    {
        $loggedUser = $this->userRepository->find($this->getUser()->getId());

        $this->deleteListedRelations($loggedUser, $user);

        if (class_exists($event = $this->getEventClass())) {
            $this->eventBus->dispatch(
                new $event(
                    $loggedUser->getId(),
                    $user->getId()
                )
            );
        }

        $this->additionalAction($user, $request);

        return $this->json([
            'success' => $this->translator->trans($this->getResponseKey()),
        ]);
    }

    protected function additionalAction(User $user, Request $request): void
    {
        //
    }

    protected function deleteListedRelations(User $first, User $second): void
    {
        foreach ($this->getLoggedUserDeleteRelations($first, $second) as $relation) {
            $this->deleteRelation($first, $second, $relation);
        }
        
        foreach ($this->getSubjectUserDeleteRelations($first, $second) as $relation) {
            $this->deleteRelation($second, $first, $relation);
        }
    }

    protected function deleteRelation(User $first, User $second, string $relation): void
    {
        $this->relationRepository->deleteWhere(
            $first->getId(), 
            $relation, 
            $this->iriConverter->getIriFromItem($second)
        );
    }

    abstract protected function getEventClass(): string;
    
    abstract protected function getLoggedUserDeleteRelations(User $user, User $subject): array;

    abstract protected function getSubjectUserDeleteRelations(User $user, User $subject): array;
    
    abstract protected function getResponseKey(): string;
}
