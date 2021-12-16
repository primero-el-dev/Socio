<?php

namespace App\Util;

class StringUtil
{
	public static function generateRandom(int $length = 80): string
	{
		return substr(bin2hex(random_bytes($length)), 0, $length);
	}

	public static function dashedToCamelCase(string $string, bool $capitalizeFirst = true): string
	{
		$str = str_replace('-', '', ucwords($string, '-'));

	    return (!$capitalizeFirst) ? lcfirst($str) : $str;
	}

	public static function dashedToSnakeCase(string $string, bool $uppercase = true): string
	{
		$str = str_replace('-', '_', $string);

	    return ($uppercase) ? strtoupper($str) : $str;
	}

	public static function camelCaseToSnakeCase(string $string, bool $uppercase = true): string
	{
		$string = preg_replace('/(?<!^)[A-Z]/', '_$0', $string);

	    return ($uppercase) ? strtoupper($string) : $string;
	}
}