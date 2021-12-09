<?php

namespace App\Controller\Auth;

use App\Repository\Interface\UserRepositoryInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class LoginController extends AbstractController
{
    public function __construct(private UserRepositoryInterface $userRepository)
    {
    }

    public function __invoke(Request $request): Response
    {
        $email = $request->request->get('email') ?? '';
        $password = $request->request->get('password') ?? '';
        $user = $this->userRepository->findOneBy(['email' => $email]);
        
        if ($user && password_verify($password, $user->getPassword())) {
            return new JsonResponse([
                'email' => $email, 
                'roles' => $user->getRoles(),
            ]);
        }

        return new JsonResponse(['error' => 'Invalid data.']);
    }
}
