<?php

namespace App\Controller\Group;

use ApiPlatform\Core\Api\IriConverterInterface;
use App\Entity\Group;
use App\Entity\User;
use App\Entity\UserSubjectRelation;
use App\Event\User\RequestGroupMembershipEvent;
use App\Repository\UserRepository;
use App\Repository\UserSubjectRelationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class RequestGroupMembershipController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private MessageBusInterface $eventBus,
        private UserRepository $userRepository,
        private UserSubjectRelationRepository $relationRepository,
        private TranslatorInterface $translator,
        private IriConverterInterface $iriConverter
    ) {
    }

    #[ParamConverter('id', class: Group::class)]
    public function __invoke(Group $group, Request $request)
    {
        $user = $this->userRepository->find($this->getUser()->getId());

        if ($this->relationRepository->userCanOn(
            $user, 'REQUEST_MEMBERSHIP', $group, false)) {
            return $this->json([
                'info' => $this->translator->trans(
                    'notification.info.groupMembershipRequested'),
            ]);
        }

        $this->createMemberShipRequestForUserAndGroup($user, $group);
        
        $this->eventBus->dispatch(
            new RequestGroupMembershipEvent(
                $user->getId(),
                $group->getName()
            )
        );

        return $this->json([
            'success' => $this->translator->trans(
                'notification.success.membershipRequestedSuccessfully'),
        ]);
    }

    private function createMemberShipRequestForUserAndGroup(User $user, Group $group): void
    {
        $relation = new UserSubjectRelation();
        $relation->setWho($user);
        $relation->setAction('REQUEST_MEMBERSHIP');
        $relation->setDenied(false);
        $relation->setSubjectIri($this->iriConverter->getIriFromItem($group));

        $this->entityManager->persist($relation);
        $this->entityManager->flush();
    }
}
