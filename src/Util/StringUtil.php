<?php

namespace App\Util;

class StringUtil
{
	public const NO_FORMAT = 0;
	public const UPPERCASE = 1;
	public const LOWERCASE = 2;

	public static function generateRandom(int $length = 80): string
	{
		return substr(bin2hex(random_bytes($length)), 0, $length);
	}

	public static function dashedToCamelCase(string $string, bool $capitalizeFirst = true): string
	{
		$str = str_replace('-', '', ucwords($string, '-'));

	    return (!$capitalizeFirst) ? lcfirst($str) : $str;
	}

	public static function dashedToSnakeCase(string $string, int $format = 1): string
	{
		$string = str_replace('-', '_', $string);

	    return self::formatString($string, $format);
	}

	public static function camelCaseToSnakeCase(string $string, int $format = 1): string
	{
		$string = preg_replace('/(?<!^)[A-Z]/', '_$0', $string);

	    return self::formatString($string, $format);
	}

	public static function camelCaseToDashed(string $string, int $format = 1): string
	{
		$string = preg_replace('/(?<!^)[A-Z]/', '-$0', $string);

	    return self::formatString($string, $format);
	}

	private static function formatString(string $string, int $format): string
	{
	    return match ($format) {
	    	self::NO_FORMAT => $string,
	    	self::UPPERCASE => strtoupper($string),
	    	self::LOWERCASE => strtolower($string)
	    };
	}
}