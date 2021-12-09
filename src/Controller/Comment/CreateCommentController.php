<?php

namespace App\Controller\Comment;

use ApiPlatform\Core\Api\IriConverterInterface;
use App\Controller\Trait\JsonRequestResponder;
use App\Http\Request\JsonExtractor;
use App\Repository\Interface\UserSubjectRelationRepositoryInterface;
use App\Util\EntityUtils;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\ORM\EntityManagerInterface;

class CreateCommentController extends AbstractController
{
    public function __construct(
        private JsonExtractor $jsonExtractor,
        private IriConverterInterface $iriConverter,
        private UserSubjectRelationRepositoryInterface $relationRepository,
        private EntityManagerInterface $entityManager
    ) {
    }

    public function __invoke(Request $request)
    {
        $user = $this->getUser();
        $data = $this->jsonExtractor->extract($request);

        $comment = new Comment();
        $comment->setContent($data['content']);
        $comment->setAuthor($user);

        $this->assignParent($comment, $data);
        $this->assignTimeline($comment, $data);
        $this->assignGroup($comment, $data);

        $this->entityManager->persist($comment);
        $this->entityManager->flush();

        return $comment;
    }

    private function assignParent(Comment &$comment, $data): void
    {
        if (!empty($data['parent'])) {
            $parent = $this->iriConverter->getItemFromIri($data['parent']);
            $comment->setParent($parent);
        }
    }

    private function assignTimeline(Comment &$comment, $data, User $user): void
    {
        if ($data['timeline'] && 
            $timeline = $this->iriConverter->getItemFromIri($data['timeline'])) {
            if (EntityUtils::areSame($timeline->getUser(), $user)) {
                $comment->setAccepted(true);
            }
            $comment->setTimeline($timeline);
        }
    }

    private function assignGroup(Comment &$comment, $data, User $user): void
    {
        if ($data['group']) {
            $group = $this->iriConverter->getItemFromIri($data['group']);
            $comment->setGroup($group);
            
            if ($user->hasRole('ROLE_ADMIN') && 
                $this->relationRepository->userCanOnSubjectIri(
                    $user, 'ROLE_ADMIN', $data['group'], false)) {
                $comment->setAccepted(true);
            }

            $this->assignThread($comment, $data);
        }
    }

    private function assignThread(Comment &$comment, $data): void
    {
        if (!empty($data['thread'])) {
            $thread = $this->iriConverter->getItemFromIri($data['group']);
            $comment->setThread($thread);
        }
    }
}
