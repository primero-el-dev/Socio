<?php

namespace App\Http\Request;

use Symfony\Component\HttpFoundation\Request;

interface DataExtractor
{
	public function extract(Request $request): array;

	public function hasData(Request $request): bool;
}