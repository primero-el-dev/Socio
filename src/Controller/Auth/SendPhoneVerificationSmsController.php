<?php

namespace App\Controller\Auth;

use App\DataValidator\PhoneDataValidator;
use App\Entity\Token;
use App\Entity\User;
use App\Http\Request\JsonExtractor;
use App\Message\User\SendSmsCommand;
use App\Repository\Interface\UserRepositoryInterface;
use App\Util\StringUtil;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class SendPhoneVerificationSmsController extends AbstractController
{
    public function __construct(
        private UserRepositoryInterface $userRepository,
        private TranslatorInterface $translator,
        private EntityManagerInterface $entityManager,
        private MessageBusInterface $commandBus,
        private JsonExtractor $jsonExtractor,
        private PhoneDataValidator $phoneValidator
    ) {
    }

    public function __invoke(Request $request): Response
    {
        $user = $this->userRepository->find($this->getUser()->getId());
        
        // if ($this->jsonExtractor->hasData($request)) {
        //     $data = $this->jsonExtractor->extract($request);

        //     if (!$this->phoneValidator->validate($data)) {
        //         return new JsonResponse([
        //             'errors' => $this->phoneValidator->getErrors(),
        //         ]);
        //     }

        //     if ($data['phone'] && $data['phone'] === $user->getPhone())
        //     $this->assignDataToUser($data, $user);
        // }

        if ($user->getPhoneVerified()) {
            return new JsonResponse([
                'info' => $this->translator->trans(
                    'notification.info.phoneAlreadyVerified'),
            ]);
        }

        if (!$user->getPhone()) {
            return new JsonResponse([
                'error' => $this->translator->trans(
                    'notification.error.phoneMissing'),
            ]);
        }

        $token = $this->createToken($user);

        $this->commandBus->dispatch(
            new SendSmsCommand($user->getId(), $token->getValue())
        );

        return new JsonResponse([
            'success' => $this->translator->trans(
                'notification.success.phoneVerificationSmsSend'),
        ]);
    }

    // public function assignDataToUser(array $data, User $user)
    // {
    //     if (!empty($data['phone'])) {
    //         $user->setPhone($data['phone']);
    //         $user->setPhoneVerified(false);
    //         $this->entityManager->flush();
    //     }
    // }

    private function createToken(User $user): Token
    {
        $token = new Token();
        $token->setType(Token::PHONE_VERIFICATION_TYPE);
        $token->setUser($user);
        $token->setValue(strtoupper(StringUtil::generateRandom(8)));
        $token->setExpiresAt(new \DateTimeImmutable('+1 minutes'));

        $this->entityManager->persist($token);
        $this->entityManager->flush();

        return $token;
    }
}
