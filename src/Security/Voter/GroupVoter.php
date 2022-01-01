<?php

namespace App\Security\Voter;

use App\Entity\Entity;
use App\Entity\Group;
use App\Entity\User;
use App\Entity\UserSubjectRelation;
use App\Repository\Interface\UserSubjectRelationRepositoryInterface;
use App\Security\PermissionManagerFacade;
use App\Security\Roles;
use App\Util\EntityUtils;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class GroupVoter extends Voter
{
    private const PERMISSIONS = [
        'read' => 'READ_GROUP',
        'create' => 'CREATE_GROUP',
        'update' => 'UPDATE_GROUP',
        'delete' => 'DELETE_GROUP',
        'request_membership' => 'REQUEST_MEMBERSHIP_GROUP',
    ];

    public function __construct(
        private UserSubjectRelationRepositoryInterface $relationRepository,
        private PermissionManagerFacade $permissionManager
    ) {
    }

    protected function supports(string $attribute, $subject): bool
    {
        return in_array($attribute, self::PERMISSIONS)
            && $subject instanceof Group;
    }

    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        if (!$user instanceof UserInterface) {
            return false;
        }

        return match ($attribute) {
            self::PERMISSIONS['read'] => $this->canRead($user, $subject),
            self::PERMISSIONS['create'] => $this->canCreate($user),
            self::PERMISSIONS['update'] => $this->canUpdate($user, $subject),
            self::PERMISSIONS['delete'] => $this->canDelete($user, $subject),
            self::PERMISSIONS['request_membership'] => 
                $this->canRequestMembership($user, $subject),
            default => false,
        };
    }

    private function canRead(User $user, Entity $subject): bool
    {
        $hasRelation = $this->hasRelation($user, $subject);
        
        return $user->hasRole('READ_GROUP') || 
            $hasRelation(UserSubjectRelation::READ_GROUP);
    }

    private function canCreate(User $user): bool
    {
        $user->addRole('CREATE_GROUP');
        dd($user);
        return $user->hasRole('CREATE_GROUP');
    }

    private function canUpdate(User $user, Entity $subject): bool
    {
        $hasRelation = $this->hasRelation($user, $subject);

        return $user->hasRole('UPDATE_GROUP') || 
            $hasRelation(UserSubjectRelation::UPDATE_GROUP);
    }

    private function canDelete(User $user, Entity $subject): bool
    {
        $hasRelation = $this->hasRelation($user, $subject);

        return $user->hasRole('REQUEST_MEMBERSHIP') || 
            $hasRelation(UserSubjectRelation::DELETE_GROUP);
    }

    private function canRequestMembership(User $user, Entity $subject): bool
    {
        $hasRelation = $this->hasRelation($user, $subject);

        return $user->hasRole('ROLE_VERIFIED') &&
            !$hasRelation(UserSubjectRelation::ROLE_USER) && 
            !$hasRelation(UserSubjectRelation::ROLE_ADMIN);
    }

    private function hasRelation(User $user, Group $subject): callable
    {
        return (fn(User $user, Group $subject) => fn(string $relation): bool =>
            $this->relationRepository->userHasRelationWith(
                $user,
                $relation,
                $subject,
                false
            ))($user, $subject);
    }
}
