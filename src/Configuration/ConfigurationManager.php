<?php

namespace App\Configuration;

class ConfigurationManager
{
	public static function getDefaultForUser(): array
	{
		return [
			'visibility' => [
				'show_name' => true,
				'show_surname' => true,
				'show_email' => true,
				'show_birth' => true,
				'show_timeline' => false,
				'show_phone' => false,
			],
		];
	}

	public static function getDefaultForGroup(): array
	{
		return [
			//
		];
	}
}