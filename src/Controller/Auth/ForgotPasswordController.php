<?php

namespace App\Controller\Auth;

use App\Controller\Trait\JsonRequestResponder;
use App\Event\User\ResetPasswordRequestEvent;
use App\Repository\Interface\UserRepositoryInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class ForgotPasswordController extends AbstractController
{
    use JsonRequestResponder;

    public function __construct(
        private TranslatorInterface $translator,
        private UserRepositoryInterface $userRepository,
        private MessageBusInterface $eventBus
    ) {
    }

    public function __invoke(Request $request): Response
    {
        $email = $this->getJson($request)['email'];

        if (empty($email) || 
            !$user = $this->userRepository->findOneBy(['email' => $email])) {
            return $this->returnJsonErrorWhenNotFoundUser();
        }

        $this->eventBus->dispatch(
            new ResetPasswordRequestEvent($user->getId())
        );
        
        return new JsonResponse(['success' => $this->translator->trans(
            'notification.success.forgotPassword')]);
    }

    private function returnJsonErrorWhenNotFoundUser(): JsonResponse
    {
        return new JsonResponse([
            'errors' => [
                'email' => $this->translator->trans(
                    'notification.success.forgotPassword'),
            ]
        ]);
    }
}
