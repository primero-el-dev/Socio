<?php

namespace App\Security\Voter;

use App\Entity\Entity;
use App\Entity\User;
use App\Repository\UserRepository;
use App\Repository\UserSubjectRelationRepository;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class ActionVoter extends Voter
{
    private const ROLES = [
        'accept_member' => 'ACCEPT_MEMBERSHIP_RELATION',
    ];

    public function __construct(
        private UserRepository $userRepository,
        private UserSubjectRelationRepository $relationRepository
    ) {
    }

    protected function supports(string $attribute, $subject): bool
    {
        [$object, $userId] = $subject;

        return in_array($attribute, self::ROLES) && 
            $object instanceof Entity;
    }

    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {
        $loggedUser = $this->userRepository->find($token->getUser()->getId());
        [$object, $userId] = $subject;
        $user = $this->userRepository->find($userId);
        
        if (!$user instanceof UserInterface) {
            return false;
        }

        return match ($attribute) {
            self::ROLES['accept_member'] => $this->canAcceptMember($loggedUser, $object, $user),
            default => false
        };
    }

    private function canAcceptMember(
        User $loggedUser, 
        Entity $object, 
        User $user
    ): bool
    {
        return $this->relationRepository->userHasRelationWith(
                $user, 
                'REQUEST_MEMBERSHIP', 
                $object,
                false
            ) &&
            (
                $loggedUser->hasRole('ROLE_ADMIN') ||
                $this->relationRepository->userHasRelationWith(
                    $loggedUser,
                    'ROLE_ADMIN',
                    $object,
                    false
                ) ||
                $this->relationRepository->userHasRelationWith(
                    $loggedUser,
                    'ACCEPT_MEMBERSHIP',
                    $object,
                    false
                )
            ) &&
            !$this->relationRepository->userHasRelationWith(
                $user,
                'ROLE_MEMBER',
                $object,
                false
            );
    }
}
