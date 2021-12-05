<?php

namespace App\Controller\User;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class MeController extends AbstractController
{
    public function __construct(private UserRepository $userRepository)
    {
    }

    public function __invoke()
    {
        return $this->userRepository->find($this->getUser()->getId());
    }
}
