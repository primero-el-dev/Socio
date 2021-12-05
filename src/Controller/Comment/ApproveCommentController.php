<?php

namespace App\Controller\Comment;

use App\Entity\Comment;
use App\Event\Comment\ApproveCommentEvent;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Messenger\MessageBusInterface;

class ApproveCommentController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private MessageBusInterface $eventBus
    ) {
    }

    #[ParamConverter('id', class: Comment::class)]
    public function __invoke(Comment $comment, Request $request)
    {
        if ($comment->getAccepted()) {
            return $comment;
        }
        
        $comment->setAccepted(true);
        $this->entityManager->flush();

        $this->eventBus->dispatch(
            new ApproveCommentEvent($comment->getId())
        );

        return $comment;
    }
}
