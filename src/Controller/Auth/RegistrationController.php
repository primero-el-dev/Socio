<?php

namespace App\Controller\Auth;

use App\Configuration\ConfigurationManager;
use App\Controller\Trait\JsonRequestResponder;
use App\DataValidator\PhoneDataValidator;
use App\DataValidator\RegistrationDataValidator;
use App\DataValidator\RepeatedPasswordDataValidator;
use App\Entity\User;
use App\Event\User\RegistrationEvent;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class RegistrationController extends AbstractController
{
    use JsonRequestResponder;

    public function __construct(
        private TranslatorInterface $translator,
        private RepeatedPasswordDataValidator $passwordValidator,
        private PhoneDataValidator $phoneValidator,
        private RegistrationDataValidator $dataValidator,
        private MessageBusInterface $eventBus,
        private EntityManagerInterface $entityManager,
        private UserPasswordHasherInterface $passwordHasher,
    ) {
    }

    public function __invoke(Request $request): Response
    {
        $data = $this->getJson($request);

        $this->dataValidator->validate($data);
        $this->passwordValidator->validate($data);
        $this->phoneValidator->validate($data);

        $errors = array_merge(
            $this->dataValidator->getErrors(), 
            $this->passwordValidator->getErrors(),
            $this->phoneValidator->getErrors()
        );

        if (!empty($errors)) {
            return new JsonResponse(['errors' => $errors]);
        }

        $user = $this->createUserFromData($data);
        
        $this->eventBus->dispatch(
            new RegistrationEvent($user->getId())
        );

        return new JsonResponse(['success' => $this->translator->trans(
            'notification.success.registered')]);
    }

    private function createUserFromData(array $data): User
    {
        $user = new User();
        $user->setLogin($data['login']);
        $user->setEmail($data['email']);
        $user->setBirth(\DateTimeImmutable::createFromFormat('Y-m-d', $data['birth']));
        $user->setName($data['name'] ?? null);
        $user->setSurname($data['surname'] ?? null);
        $user->setPhone($data['phone'] ?? null);
        $user->setSlug($data['slug'] ?? null);
        $hashedPassword = $this->passwordHasher->hashPassword($user, $data['password']);
        $user->setConfiguration(ConfigurationManager::getDefaultForUser());
        $user->setPassword($hashedPassword);
        $user->setConfiguration(ConfigurationManager::getDefaultForUser());

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return $user;
    }
}
