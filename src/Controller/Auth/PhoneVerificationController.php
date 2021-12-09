<?php

namespace App\Controller\Auth;

use App\Entity\Token;
use App\Entity\User;
use App\Http\Request\JsonExtractor;
use App\Repository\Interface\TokenRepositoryInterface;
use App\Repository\Interface\UserRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\Translation\TranslatorInterface;

class PhoneVerificationController extends AbstractController
{
    public function __construct(
        private JsonExtractor $jsonExtractor,
        private TokenRepositoryInterface $tokenRepository,
        private UserRepositoryInterface $userRepository,
        private EntityManagerInterface $entityManager,
        private TranslatorInterface $translator
    ) {
    }

    public function __invoke(Request $request): Response
    {
        $user = $this->userRepository->find($this->getUser()->getId());

        if ($user->getPhoneVerified()) {
            return new JsonResponse([
                'info' => $this->translator->trans(
                    'notification.info.phoneAlreadyVerified'),
            ]);
        }

        $data = $this->jsonExtractor->extract($request);

        if (empty($data['code'])) {
            return new JsonResponse([
                'errors' => [
                    'code' => $this->translator->trans(
                        'notification.error.smsCodeMissing')
                ],
            ]);
        }

        if (!preg_match('/^[0-9A-Z]+$/', $data['code']) ||  
            !($token = $this->popTokenForUser($data['code'], $user)) ||
            $token->hasExpired()) {
            return new JsonResponse([
                'errors' => [
                    'code' => $this->translator->trans(
                        'notification.error.smsCodeInvalid')
                ],
            ]);
        }
        
        $user->setPhoneVerified(true);
        $this->entityManager->flush();

        return new JsonResponse([
            'success' => $this->translator->trans(
                'notification.success.phoneVerifiedSuccessfully')
        ]);
    }

    private function popTokenForUser(string $value, User $user): ?Token
    {
        $token = $this->tokenRepository->getTokenByTypeValueUser(
            Token::PHONE_VERIFICATION_TYPE,
            $value,
            $user
        );
        $this->tokenRepository->deleteByTypeAndUserId(
            Token::PHONE_VERIFICATION_TYPE,
            $user->getId()
        );

        return $token;
    }
}
