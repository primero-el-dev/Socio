<?php

namespace App\Controller\Auth;

use App\Controller\Trait\JsonRequestResponder;
use App\DataValidator\RepeatedPasswordDataValidator;
use App\Form\ResetPasswordType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;
use Symfony\Contracts\Translation\TranslatorInterface;

class ResetPasswordController extends AbstractController
{
    use JsonRequestResponder;

    public function __construct(
        private Security $security,
        private EntityManagerInterface $entityManager,
        private UserPasswordHasherInterface $passwordHasher,
        private TranslatorInterface $translator,
        private FormFactoryInterface $formFactory,
        private UserRepository $userRepository,
        private RepeatedPasswordDataValidator $dataValidator
    ) {
    }

    public function __invoke(Request $request)
    {
        if (!$user = $this->security->getUser()) {
            throw new AccessDeniedException();
        }

        $user = $this->userRepository->find($user->getId());
        $data = $this->getJson($request);

        if (!$this->dataValidator->validate($data)) {
            return new JsonResponse(['errors' => $this->dataValidator->getErrors()]);
        }

        $hashedPassword = $this->passwordHasher->hashPassword($user, $data['password']);
        $user->setPassword($hashedPassword);
        $this->entityManager->flush();

        return new JsonResponse(['success' => $this->translator->trans(
            'notification.success.passwordChanged')]);
    }
}
