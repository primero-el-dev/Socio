<?php

namespace App\Controller;

use ApiPlatform\Core\Api\IriConverterInterface;
use ApiPlatform\Core\Api\UrlGeneratorInterface;
use App\Entity\PermissionConfiguration;
use App\Entity\User;
use App\Event\User\ChangePasswordEvent;
use App\Event\User\RegistrationEvent;
use App\Repository\Interface\CommentRepositoryInterface;
use App\Repository\Interface\UserRepositoryInterface;
use App\Repository\Interface\PostRepositoryInterface;
use App\Repository\Interface\ReactionRepositoryInterface;
use App\Repository\Interface\TimelineRepositoryInterface;
use App\Repository\Interface\TokenRepositoryInterface;
use App\Repository\Interface\UserRepositoryInterface;
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
		// private UserRepository $userRepository,
		private PostRepositoryInterface $postRepository,
		private TokenRepositoryInterface $tokenRepository,
		private ReactionRepositoryInterface $reactionRepository,
		private CommentRepositoryInterface $commentRepository,
		private IriConverterInterface $iriConverter,
		private MessageBusInterface $eventBus,
		private MailerInterface $mailer,
		private UrlGeneratorInterface $urlGenerator,
		private ValidatorInterface $validator,
		private TranslatorInterface $translator,
		private TimelineRepository $timelineRepository,
		private NotifierInterface $notifier,
		private UserRepositoryInterface $userRepository
	) {
	}

	public function __invoke(Request $request)
	{
		$user = $this->userRepository->find(2);
		// $user->setConfigurationKeys(['a', 'b', 'c'], 'value');
		dd($user);

		// $notification = (new Notification('New Invoice', ['sms/sinch']))
  //           ->content('You got a new invoice for 15 EUR.');

  //       // The receiver of the Notification
  //       $recipient = new Recipient(
  //           '1234567890localhost@gmail.com',
  //           '48536343411'
  //       );

  //       // Send the notification to the recipient
  //       $this->notifier->send($notification, $recipient);

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
}