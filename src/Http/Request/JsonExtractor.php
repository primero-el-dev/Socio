<?php

namespace App\Http\Request;

use App\Http\Request\DataExtractor;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class JsonExtractor implements DataExtractor
{
	public function extract(Request $request): array
	{
		$data = json_decode($request->getContent(), true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new BadRequestHttpException('Invalid json', 400);
        }

        return (array) $data;
	}

	public function hasData(Request $request): bool
	{
		return !empty($request->getContent());
	}
}