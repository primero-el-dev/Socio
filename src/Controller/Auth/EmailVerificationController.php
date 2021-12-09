<?php

namespace App\Controller\Auth;

use App\Entity\Token;
use App\Entity\User;
use App\Repository\Interface\TokenRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Contracts\Translation\TranslatorInterface;

class EmailVerificationController extends AbstractController
{
    public function __construct(
        private TokenRepositoryInterface $tokenRepository,
        private EntityManagerInterface $entityManager,
        private TranslatorInterface $translator
    ) {
    }

    public function __invoke(Request $request): Response
    {
        $token = $this->tokenRepository->getTokenByTypeAndValue(
            'EMAIL_VERIFICATION_TOKEN', 
            $request->query->get('token') ?? ''
        );

        if (!$token) {
            throw new NotFoundHttpException();
        }
        
        $user = $token->getUser();
        $this->handleUser($user);

        $this->tokenRepository->deleteByTypeAndUserId(
            Token::EMAIL_VERIFICATION_TYPE, 
            $user->getId()
        );

        return new JsonResponse([
            'success' => $this->translator->trans('notification.success.verifyEmail'),
        ]);
    }

    private function handleUser(User $user)
    {
        $user->setVerified(true);
        $user->addRole('ROLE_VERIFIED');
        $this->entityManager->flush();
    }
}
