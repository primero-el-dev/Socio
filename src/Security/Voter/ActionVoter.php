<?php

namespace App\Security\Voter;

use App\Entity\Entity;
use App\Entity\User;
use App\Repository\Interface\UserRepositoryInterface;
use App\Repository\Interface\UserSubjectRelationRepositoryInterface;
use App\Util\EntityUtils;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class ActionVoter extends Voter
{
    private const ROLES = [
        'accept_member' => 'ACCEPT_MEMBERSHIP_RELATION',
        'remove_member' => 'REMOVE_MEMBERSHIP_RELATION',
    ];

    public function __construct(
        private UserRepositoryInterface $userRepository,
        private UserSubjectRelationRepositoryInterface $relationRepository
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
            self::ROLES['remove_member'] => $this->canRemoveMember($loggedUser, $object, $user),
            default => false
        };
    }

    private function canAcceptMember(
        User $loggedUser, 
        Entity $object, 
        User $user
    ): bool
    {
        $hasRelation = $this->hasRelationClosure($object);

        return $hasRelation($user, 'REQUEST_MEMBERSHIP') &&
            (
                $loggedUser->hasRole('ROLE_ADMIN') ||
                $hasRelation($loggedUser, 'ROLE_ADMIN') ||
                $hasRelation($loggedUser, 'ACCEPT_MEMBERSHIP')
            ) &&
            !$hasRelation($user, 'ROLE_MEMBER');
    }

    private function canRemoveMember(
        User $loggedUser, 
        Entity $object, 
        User $user
    ): bool
    {
        $hasRelation = $this->hasRelationClosure($object);

        return $hasRelation($user, 'ROLE_MEMBER') &&
            (
                EntityUtils::areSame($loggedUser, $user) ||
                $loggedUser->hasRole('ROLE_ADMIN') ||
                $hasRelation($loggedUser, 'ROLE_ADMIN') ||
                $hasRelation($loggedUser, 'REMOVE_MEMBERSHIP')
            );
    }

    private function hasRelationClosure(Entity $subject): callable
    {
        return function (User $user, string $relation) use ($subject): bool {
            return $this->relationRepository->userHasRelationWith(
                $user,
                $relation,
                $subject,
                false
            );
        };
    }
}
