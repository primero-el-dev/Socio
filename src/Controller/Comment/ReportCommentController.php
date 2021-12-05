<?php

namespace App\Controller\Comment;

use App\Entity\Comment;
use App\Event\Comment\ReportCommentEvent;
use App\Repository\UserSubjectRelationRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class ReportCommentController extends AbstractController
{
    public function __construct(
        private MessageBusInterface $eventBus,
        private TranslatorInterface $translator
    ) {
    }

    #[ParamConverter('id', class: Comment::class)]
    public function __invoke(Comment $comment, Request $request)
    {
        $user = $this->getUser();

        $this->eventBus->dispatch(
            new ReportCommentEvent($user->getId(), $comment->getId())
        );

        return new JsonResponse([
            'success' => $this->translator->trans(
                'notification.success.commentAlreadyReported'),
        ]);
    }
}
