<?php

namespace App\Security\Voter\Relation;

use ApiPlatform\Core\Api\IriConverterInterface;
use App\Entity\User;
use App\Repository\Interface\UserSubjectRelationRepositoryInterface;
use App\Util\EntityUtils;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

abstract class RelationVoter extends Voter
{
    public function __construct(
        protected IriConverterInterface $iriConverter,
        protected UserSubjectRelationRepositoryInterface $relationRepository
    ) {
    }

    protected function supports(string $attribute, $subject): bool
    {
        return in_array($attribute, $this->getRoles())
            && $subject instanceof User;
    }

    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();
        
        if (!$user instanceof UserInterface) {
            return false;
        }

        return match ($attribute) {
            $this->getRoles()['request_relation'] => $this->canRequestRelation($user, $subject),
            $this->getRoles()['accept_relation'] => $this->canAcceptRelation($user, $subject),
            $this->getRoles()['break_relation'] => $this->canBreakRelation($user, $subject),
            default => false,
        };
    }

    protected function canRequestRelation(User $user, User $subject): bool
    {
        $hasRelation = $this->hasRelation();

        return !EntityUtils::areSame($user, $subject) && 
            !$hasRelation($user, $this->getRelationRequest(), $subject) &&
            !$hasRelation($user, $this->getRealizedRelation(), $subject);
    }

    protected function canAcceptRelation(User $user, User $subject): bool
    {
        $hasRelation = $this->hasRelation();

        return !EntityUtils::areSame($user, $subject) && 
            $hasRelation($subject, $this->getRelationRequest(), $user) &&
            !$hasRelation($subject, $this->getRealizedRelation(), $user) &&
            $this->additionalRestrictions($user, $subject);
    }

    protected function additionalRestrictions(User $user, User $subject): bool
    {
        return true;
    }

    protected function canBreakRelation(User $user, User $subject): bool
    {
        $hasRelation = $this->hasRelation();

        return !EntityUtils::areSame($user, $subject) &&
            $hasRelation($user, $this->getRealizedRelation(), $subject);
    }

    protected function hasRelation(): callable
    {
        return fn(User $user, string $relation, User $subject): bool =>
            $this->relationRepository->userHasRelationWith(
                $user,
                $relation,
                $subject,
                false
            );
    }

    abstract protected function getRelationRequest(): string;

    abstract protected function getRealizedRelation(): string;

    abstract protected function getRoles(): array;
}
