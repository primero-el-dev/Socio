<?php

namespace App\Controller\User;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UploadProfilerPictureController extends AbstractController
{
    public function index(): Response
    {
        return $this->render('user/upload_profiler_picture/index.html.twig', [
            'controller_name' => 'UploadProfilerPictureController',
        ]);
    }
}
