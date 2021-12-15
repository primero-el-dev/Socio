<?php

namespace App\Security\Voter;

use ApiPlatform\Core\Api\IriConverterInterface;
use App\Entity\User;
use App\Entity\UserSubjectRelation;
use App\Repository\Interface\UserSubjectRelationRepositoryInterface;
use App\Util\EntityUtils;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class UserVoter extends Voter
{
    private const ROLES = [
        'request_friendship' => 'REQUEST_FRIENDSHIP_USER',
        'accept_friendship' => 'ACCEPT_FRIENDSHIP_USER',
        'break_friendship' => 'BREAK_FRIENDSHIP_USER',
    ];

    public function __construct(
        private IriConverterInterface $iriConverter,
        private UserSubjectRelationRepositoryInterface $relationRepository
    ) {
    }

    protected function supports(string $attribute, $subject): bool
    {
        return in_array($attribute, self::ROLES)
            && $subject instanceof User;
    }

    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();
        
        if (!$user instanceof UserInterface) {
            return false;
        }

        return match ($attribute) {
            self::ROLES['request_friendship'] => $this->canRequestFriendship($user, $subject),
            self::ROLES['accept_friendship'] => $this->canBreakFriendship($user, $subject),
            self::ROLES['break_friendship'] => $this->canBreakFriendship($user, $subject),
            default => false,
        };
    }

    private function canRequestFriendship(User $user, User $subject): bool
    {
        $hasRelation = $this->hasRelation();

        return !EntityUtils::areSame($user, $subject) && 
            $hasRelation($subject, UserSubjectRelation::REQUEST_FRIENDSHIP, $user) &&
            !$hasRelation($user, UserSubjectRelation::FRIEND, $subject);
    }

    private function canAcceptFriendship(User $user, User $subject): bool
    {
        $hasRelation = $this->hasRelation();

        return !EntityUtils::areSame($user, $subject) && 
            $hasRelation($subject, UserSubjectRelation::REQUEST_FRIENDSHIP, $user) &&
            !$hasRelation($user, UserSubjectRelation::FRIEND, $subject);
    }

    private function canBreakFriendship(User $user, User $subject): bool
    {
        $hasRelation = $this->hasRelation();

        return !EntityUtils::areSame($user, $subject) &&
            $hasRelation($user, UserSubjectRelation::FRIEND, $subject);
    }

    private function hasRelation(): callable
    {
        return fn(User $user, string $relation, User $subject): bool =>
            $this->relationRepository->userHasRelationWith(
                $user,
                $relation,
                $subject,
                false
            );
    }
}
