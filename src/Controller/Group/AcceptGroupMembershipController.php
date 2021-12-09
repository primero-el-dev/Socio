<?php

namespace App\Controller\Group;

use ApiPlatform\Core\Api\IriConverterInterface;
use App\Entity\Group;
use App\Entity\User;
use App\Entity\UserSubjectRelation;
use App\Event\User\AcceptGroupMembershipEvent;
use App\Repository\Interface\UserRepositoryInterface;
use App\Security\PermissionManagerFacade;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Contracts\Translation\TranslatorInterface;

class AcceptGroupMembershipController extends AbstractController
{
    public function __construct(
        private TranslatorInterface $translator,
        private UserRepositoryInterface $userRepository,
        private PermissionManagerFacade $permissionManager
    ) {
    }

    #[ParamConverter('id', class: Group::class)]
    public function __invoke(Group $group, int $userId, Request $request)
    {
        $user = $this->userRepository->find($userId);
        $this->permissionManager->grantOnGroupJoin($user, $group);

        return $this->json([
            'success' => sprintf(
                $this->translator->trans('notification.success.groupMemberAccepted'),
                $group->getName()
            ),
        ]);
    }
}
