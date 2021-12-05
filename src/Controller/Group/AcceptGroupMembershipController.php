<?php

namespace App\Controller\Group;

use ApiPlatform\Core\Api\IriConverterInterface;
use App\Entity\Group;
use App\Entity\User;
use App\Entity\UserSubjectRelation;
use App\Event\User\AcceptGroupMembershipEvent;
use App\Repository\UserRepository;
use App\Repository\UserSubjectRelationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class AcceptGroupMembershipController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private MessageBusInterface $eventBus,
        private UserSubjectRelationRepository $relationRepository,
        private TranslatorInterface $translator,
        private IriConverterInterface $iriConverter,
        private UserRepository $userRepository
    ) {
    }

    #[ParamConverter('id', class: Group::class)]
    public function __invoke(Group $group, int $userId, Request $request)
    {
        $loggedUser = $this->userRepository->find($this->getUser()->getId());
        $checkRelation = $this->getCheckingRelationFunction($group);
        $user = $this->userRepository->find($userId);

        if ((!$checkRelation($loggedUser, 'ROLE_ADMIN') &&
            !$loggedUser->hasRole('ROLE_ADMIN')) ||
            $checkRelation($user, 'ROLE_MEMBER')) {
            throw new AccessDeniedException();
        }

        if (!$checkRelation($user, 'REQUEST_MEMBERSHIP')) {
            return $this->json([
                'error' => $this->translator->trans(
                    'notification.error.groupMembershipRequestMissing'),
            ]);
        }

        $this->createPermissions($user, $group);
        
        $this->eventBus->dispatch(
            new AcceptGroupMembershipEvent(
                $user->getId(),
                $group->getName()
            )
        );

        return $this->json([
            'success' => sprintf(
                $this->translator->trans('notification.success.groupMemberAccepted'),
                $group->getName()
            ),
        ]);
    }

    private function getCheckingRelationFunction(Group $group): callable
    {
        return (fn($group) => fn(User $user, string $relation) =>
            $this->relationRepository->userHasRelationWith(
                $user, $relation, $group, false
            )
        )($group);
    }

    private function createPermissions(User $user, Group $group): void
    {
        $this->createRelation($user, UserSubjectRelation::ROLE_MEMBER, $group);
        $this->createRelation($user, UserSubjectRelation::READ_COMMENT, $group);
        $this->createRelation($user, UserSubjectRelation::CREATE_COMMENT, $group);
        $this->createRelation($user, UserSubjectRelation::REACT_COMMENT, $group);
        $this->createRelation($user, UserSubjectRelation::REPORT_COMMENT, $group);
        $this->entityManager->flush();
    }    

    private function createRelation(User $user, string $action, Group $group): void
    {
        $relation = new UserSubjectRelation();
        $relation->setWho($user);
        $relation->setAction(UserSubjectRelation::ROLE_MEMBER);
        $relation->setDenied(false);
        $relation->setSubjectIri($this->iriConverter->getIriFromItem($group));

        $this->entityManager->persist($relation);
    }
}
