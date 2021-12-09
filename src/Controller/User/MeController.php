<?php

namespace App\Controller\User;

use App\Repository\Interface\UserRepositoryInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class MeController extends AbstractController
{
    public function __construct(private UserRepositoryInterface $userRepository)
    {
    }

    public function __invoke()
    {
        return $this->userRepository->find($this->getUser()->getId());
    }
}
