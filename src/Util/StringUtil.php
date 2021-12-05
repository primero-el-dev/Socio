<?php

namespace App\Util;

class StringUtil
{
	public static function generateRandom(int $length = 80): string
	{
		return substr(bin2hex(random_bytes($length)), 0, $length);
	}
}