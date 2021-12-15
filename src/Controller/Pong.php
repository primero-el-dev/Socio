<?php

namespace App\Controller;

use ApiPlatform\Core\Api\IriConverterInterface;
use ApiPlatform\Core\Api\UrlGeneratorInterface;
use App\Entity\PermissionConfiguration;
use App\Entity\Relation;
use App\Entity\User;
use App\Entity\UserSubjectRelation;
use App\Event\User\ChangePasswordEvent;
use App\Event\User\RegistrationEvent;
use App\Repository\Interface\CommentRepositoryInterface;
use App\Repository\Interface\PostRepositoryInterface;
use App\Repository\Interface\ReactionRepositoryInterface;
use App\Repository\Interface\TimelineRepositoryInterface;
use App\Repository\Interface\TokenRepositoryInterface;
use App\Repository\Interface\UserRepositoryInterface;
use App\Repository\Interface\UserSubjectRelationRepositoryInterface;
use App\Security\Roles;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Notifier\Notification\Notification;
use Symfony\Component\Notifier\NotifierInterface;
use Symfony\Component\Notifier\Recipient\Recipient;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class Pong extends AbstractController
{
	public function __construct(
		private EntityManagerInterface $entityManager,
		private TokenRepositoryInterface $tokenRepository,
		private ReactionRepositoryInterface $reactionRepository,
		private CommentRepositoryInterface $commentRepository,
		private IriConverterInterface $iriConverter,
		private MessageBusInterface $eventBus,
		private MailerInterface $mailer,
		private UrlGeneratorInterface $urlGenerator,
		private ValidatorInterface $validator,
		private TranslatorInterface $translator,
		private TimelineRepositoryInterface $timelineRepository,
		private NotifierInterface $notifier,
		private UserRepositoryInterface $userRepository,
		private UserSubjectRelationRepositoryInterface $relationRepository
	) {
	}

	public function __invoke(Request $request)
	{
		$first = $this->userRepository->find(2);
		$second = $this->userRepository->find(13);

		// $this->deleteFriendship($first, $second);
		$this->createFriends($first, $second);

		return new JsonResponse([]);
	}

    private function generateLink(string $token): string
    {
    	return $this->urlGenerator->generate('api_email_verification_check', [
    		'token' => $token,
    	], UrlGeneratorInterface::ABS_URL);
    }

	public function sendEmail()
    {
        $email = (new TemplatedEmail())
            ->from($_ENV['MAILER_DSN'])
            ->to('1234567890localhost@gmail.com')
            ->subject('Time for Symfony Mailer!')
            ->htmlTemplate('emails/registration_confirmation.html.twig');

        $this->mailer->send($email);
    }

    private function deleteFriendship(User $first, User $second): void
    {
        $this->relationRepository->deleteWhere(
            $first->getId(), 
            UserSubjectRelation::FRIEND, 
            $this->iriConverter->getIriFromItem($second)
        );
        $this->relationRepository->deleteWhere(
            $second->getId(), 
            UserSubjectRelation::REQUEST_FRIEND, 
            $this->iriConverter->getIriFromItem($first)
        );
    }

    private function createFriends(User $first, User $second): void
    {
    	// $this->createRelation($first, $second, UserSubjectRelation::REQUEST_FRIEND);
    	$this->createRelation($second, $first, UserSubjectRelation::FRIEND);
    }

    private function createRelation(User $first, User $second, string $action): void
    {
    	$relation = new UserSubjectRelation();
    	$relation->setWho($first);
    	$relation->setAction($action);
    	$relation->setSubjectIri($this->iriConverter->getIriFromItem($second));
    	$relation->setDenied(false);

    	$this->entityManager->persist($relation);
    	$this->entityManager->flush();
    }
}