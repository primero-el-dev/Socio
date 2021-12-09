<?php

namespace App\Controller\Group;

use App\Entity\Group;
use App\Repository\Interface\UserRepositoryInterface;
use App\Security\PermissionManagerFacade;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class RemoveGroupMemberController extends AbstractController
{
    public function __construct(
        private UserRepositoryInterface $userRepository,
        private PermissionManagerFacade $permissionManager
    ) {
    }

    #[ParamConverter('id', class: Group::class)]
    public function __invoke(Group $group, int $userId, Request $request): Response
    {
        $user = $this->userRepository->find($userId);
        $this->permissionManager->removeGrantsOnGroupQuit($user, $group);

        return $this->json([
            'success' => $this->translator->trans(
                ($this->getUser()->getId() === $userId) 
                    ? 'notification.success.quitGroup'
                    : 'notification.success.removeMember'
            ),
        ]);
    }
}
