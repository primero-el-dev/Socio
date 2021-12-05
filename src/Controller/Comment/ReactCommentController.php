<?php

namespace App\Controller\Comment;

use App\Entity\Comment;
use App\Entity\Reaction;
use App\Repository\ReactionRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ReactCommentController extends AbstractController
{
    public function __construct(
        private ReactionRepository $reactionRepository,
        private UserRepository $userRepository,
        private EntityManagerInterface $entityManager
    ) {
    }

    #[ParamConverter('id', class: Comment::class)]
    public function __invoke(Comment $comment, Request $request)
    {
        $type = strtoupper($request->attributes->get('reaction'));
        
        if (!in_array($type, Reaction::TYPES)) {
            throw new NotFoundHttpException();
        }

        $user = $this->userRepository->find($this->getUser()->getId());
        $reaction = $this->reactionRepository->getForUserAndComment($user, $comment);

        $this->reactionRepository->deleteForUserAndComment($user, $comment);

        if (!$reaction || $reaction->getType() !== $type) {
            $reaction = new Reaction();
            $reaction->setType($type);
            $reaction->setAuthor($user);
            $reaction->setComment($comment);
            $this->entityManager->flush();
        }

        return $reaction;
    }
}
