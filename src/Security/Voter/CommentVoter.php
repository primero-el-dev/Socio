<?php

namespace App\Security\Voter;

use ApiPlatform\Core\Api\IriConverterInterface;
use App\Entity\Comment;
use App\Entity\Entity;
use App\Entity\Timeline;
use App\Entity\User;
use App\Http\Request\JsonExtractor;
use App\Repository\Interface\UserSubjectRelationRepositoryInterface;
use App\Security\Roles;
use App\Util\EntityUtils;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class CommentVoter extends Voter
{
    private const PERMISSIONS = [
        'list' => 'LIST_COMMENT',
        'read' => 'READ_COMMENT',
        'create' => 'CREATE_COMMENT',
        'update' => 'UPDATE_COMMENT',
        'delete' => 'DELETE_COMMENT',
        'react' => 'REACT_COMMENT',
        'report' => 'REPORT_COMMENT',
        'approve' => 'APPROVE_COMMENT',
        'ban' => 'BAN_COMMENT',
    ];

    public function __construct(
        private UserSubjectRelationRepositoryInterface $relationRepository,
        private RequestStack $requestStack,
        private JsonExtractor $jsonExtractor,
        private IriConverterInterface $iriConverter
    ) {
    }

    protected function supports(string $attribute, $subject): bool
    {
        return in_array($attribute, self::PERMISSIONS);
    }

    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        if (!$user instanceof UserInterface) {
            return false;
        }

        return match ($attribute) {
            self::PERMISSIONS['list'] => $this->canList($user),
            self::PERMISSIONS['read'] => $this->canRead($user, $subject),
            self::PERMISSIONS['create'] => $this->canCreate($user),
            self::PERMISSIONS['update'] => $this->canUpdate($user, $subject),
            self::PERMISSIONS['delete'] => $this->canDelete($user, $subject),
            self::PERMISSIONS['react'] => $this->canReact($user, $subject),
            self::PERMISSIONS['report'] => $this->canReport($user, $subject),
            self::PERMISSIONS['approve'] => $this->canApprove($user, $subject),
            self::PERMISSIONS['ban'] => $this->canBan($user, $subject),
            default => false,
        };
    }

    public function canList(User $user): bool
    {
        $iri = $this->requestStack->getMasterRequest()->query->get('references');
        $item = $this->iriConverter->getItemFromIri($iri);

        if ($item instanceof Timeline &&
            EntityUtils::areSame($item->getUser(), $user)) {
            return true;
        }

        return $user->hasRole('READ_COMMENT') || 
            $this->relationRepository->userCanOnSubjectIri(
                $user, 
                'READ_COMMENT', 
                $iri
            );
    }

    private function canRead(User $user, Comment $subject): bool
    {
        return ($user->hasRole('READ_COMMENT') || 
            $this->userCanOnComment($user, 'READ_COMMENT', $subject));
    }

    private function commentActionReferencingEntityIsNotDeniedForUser(
        Comment $comment, 
        string $action,
        string $method, 
        User $user
    ): bool
    {
        if (!method_exists($comment, $method) || !$entity = $comment->{$method}()) {
            return true;
        }

        return $this->relationRepository->userCanOnSubjectIri(
            $user, 
            $action, 
            $this->iriConverter->getIriFromItem($entity)
        );
    }

    private function commentReadReferencingEntityIsNotDeniedForUser(
        Comment $comment, 
        string $method, 
        User $user
    ): bool
    {
        return $this->commentActionReferencingEntityIsNotDeniedForUser(
            $comment, 'READ_COMMENT', $method, $user);
    }

    private function canCreate(User $user): bool
    {
        $data = $this->getRequestData();

        return $user->hasRole('CREATE_COMMENT') ||
            $this->userCanOnData($user, 'CREATE_COMMENT', $data);
    }

    public function isUsersTimelineIriIfExists(User $user, ?string $timelineIri): bool
    {
        if ($timelineIri === null) {
            return true;
        }

        return EntityUtils::areSame(
            $this->iriConverter->getItemFromIri($timelineIri)->getUser(), 
            $user
        );
    }

    private function entityCommentIsNotDeniedForUser(
        array $data, 
        string $key, 
        User $user
    ): bool
    {
        if (!isset($data[$key]) || is_null($data[$key])) {
            return true;
        }

        // To throw exception if not found
        $this->iriConverter->getItemFromIri($data[$key]);

        return $this->relationRepository->userCanOnSubjectIri(
            $user, 
            'CREATE_COMMENT', 
            $data[$key]
        );
    }

    private function canUpdate(User $user, Entity $subject): bool
    {
        return $user->hasRole('UPDATE_COMMENT') || 
            EntityUtils::areSame($subject->getAuthor(), $user);
    }

    private function canDelete(User $user, Entity $subject): bool
    {
        return $user->hasRole('DELETE_COMMENT') || 
            EntityUtils::areSame($subject->getAuthor(), $user);
    }

    private function canReact(User $user, Entity $subject): bool
    {
        return $user->hasRole('REACT_COMMENT') || 
            $this->userCanOnComment($user, 'REACT_COMMENT', $subject);
    }

    private function canReport(User $user, Entity $subject): bool
    {
        return $user->hasRole('REPORT_COMMENT') || 
            $user->hasRole('ROLE_ADMIN') ||
            $this->userCanOnComment($user, 'REPORT_COMMENT', $subject) ||
            $this->userCanOn($user, 'REPORT_COMMENT', $subject->getGroup()) ||
            $this->userCanOn($user, 'ROLE_ADMIN', $subject->getGroup()) ||
            $this->userCanOn($user, 'REPORT_COMMENT', $subject->getTimeline());
    }

    private function canApprove(User $user, Entity $subject): bool
    {
        return $user->hasRole('APPROVE_COMMENT') || 
            $user->hasRole('ROLE_ADMIN') ||
            $this->userCanOn($user, 'APPROVE_COMMENT', $subject->getGroup()) ||
            $this->userCanOn($user, 'ROLE_ADMIN', $subject->getGroup());
    }

    private function canBan(User $user, Entity $subject): bool
    {
        return $user->hasRole('BAN_COMMENT') || 
            $user->hasRole('ROLE_ADMIN') ||
            $this->userCanOn($user, 'BAN_COMMENT', $subject->getGroup()) ||
            $this->userCanOn($user, 'ROLE_ADMIN', $subject->getGroup());
    }

    private function userCanOnComment(
        User $user, 
        string $action, 
        comment $comment
    ): bool
    {
        $result = false;

        if (EntityUtils::areSame($comment->getAuthor(), $user)) {
            return true;
        }

        $timeline = $comment->getTimeline();
        $author = $comment->getAuthor();
        
        if ($timeline && 
            (EntityUtils::areSame($timeline->getUser(), $user) || 
            $author->getComfigurationValue(['visibility', 'show_timeline']))) {
            return true;
        }
        
        foreach (['getGroup', 'getTimeline'] as $method) {
            $result = $result || 
                (($entity = $comment->{$method}()) 
                    ? $this->userCanOn($user, $action, $entity)
                    : true);
        }

        if ($data['parent']) {
            $parent = $this->iriConverter->getItemFromIri($data['parent']);

            if (!EntityUtils::areSame($comment->getGroup(), $parent->getGroup()) ||
                !EntityUtils::areSame($comment->getGroup(), $parent->getGroup())) {
                return false;
            }
        }

        return $result;
    }

    private function userCanOnData(
        User $user, 
        string $action, 
        array $data
    ): bool
    {
        $result = false;

        if (!empty($data['timeline']) && !empty($data['group'])) {
            return false;
        }
        
        if (!empty($data['thread'])) {
            $thread = $this->iriConverter->getItemFromIri($data['thread']);
            $groupIri = $this->iriConverter->getIriFromItem($thread->getGroup());
            
            if (empty($data['group']) || $data['group'] !== $groupIri) {
                return false;
            }
        }

        if ($this->isUsersTimelineIriIfExists($user, $data['timeline'] ?? null)) {
            return true;
        }

        if (!empty($data['group']) && !empty($data['timeline'])) {
            return false;
        }

        foreach (['group', 'timeline'] as $key) {
            if ($data[$key] && is_string($data[$key]) && 
                !$this->userCanOnIri($user, $action, $data[$key])) {
                return false;
            }
        }

        return true;
    }

    private function userCanOnCommentInTimelineIri(
        User $user, 
        string $action, 
        Comment $comment, 
        ?string $timelineIri
    ): bool
    {
        if ($timelineIri === null || 
            $this->isUsersTimelineIriIfExists($user, $timelineIri ?? null)) {
            return true;
        }

        return $this->userCanOnIri($user, $action, $timelineIri);
    }

    private function userCanOn(User $user, string $action, ?Entity $entity): bool
    {
        if (!$entity) {
            return false;
        }

        return $this->userCanOnIri(
            $user, 
            $action, 
            $this->iriConverter->getIriFromItem($entity)
        );
    }

    private function userCanOnIri(User $user, string $action, string $iri): bool
    {
        return $this->relationRepository->userCanOnSubjectIri(
            $user, 
            $action, 
            $iri,
            false
        );
    }

    private function getRequestData(): array
    {
        return $this->jsonExtractor->extract(
            $this->requestStack->getMasterRequest()
        );
    }
}
