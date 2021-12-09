<?php

namespace App\Controller\Auth;

use App\Entity\Token;
use App\Repository\Interface\TokenRepositoryInterface;
use App\Repository\Interface\UserRepositoryInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ResetPasswordPageController extends AbstractController
{
    public function __construct(
        private UserRepositoryInterface $userRepository,
        private TokenRepositoryInterface $tokenRepository
    ) {
    }

    public function __invoke(Request $request): Response
    {
        $this->handleTokenFromRequest($request);

        return $this->render('auth/reset_password_page/index.html.twig', [
            'controller_name' => 'ResetPasswordPageController',
        ]);
    }

    public function handleTokenFromRequest(Request $request): Token
    {
        $token = $this->tokenRepository->getTokenByTypeAndValue(
            Token::RESET_PASSWORD_TYPE, 
            $request->query->get('token') ?? ''
        );

        if (!$token) {
            throw new NotFoundHttpException();
        }

        $this->tokenRepository->deleteByTypeAndUserId(
            Token::RESET_PASSWORD_TYPE, 
            $token->getUser()->getId()
        );

        return $token;
    }
}
