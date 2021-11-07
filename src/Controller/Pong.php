<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

class Pong extends AbstractController
{
	public function __invoke(Request $request)
	{
		dd('ok');
	}
}