<?php

namespace App\Entity;

class PermissionConfiguration
{
	public function __construct(private array $items = [])
	{
	}

	public static function getDefaultForUser(): array
	{
		return [
			'POST_SHOW' => true,
			'POST_CREATE' => true,
			'GROUP_SHOW' => true,
			'GROUP_CREATE' => true,
			'TOPIC_SHOW' => true,
			'TOPIC_CREATE' => true,
			'COMMENT_SHOW' => true,
			'COMMENT_CREATE' => true,
		];
	}

	public static function getDefaultForAdmin(): array
	{
		return [
			'POST_SHOW' => true,
			'POST_CREATE' => true,
			'POST_UPDATE' => true,
			'POST_DELETE' => true,
			'GROUP_SHOW' => true,
			'GROUP_CREATE' => true,
			'GROUP_UPDATE' => true,
			'GROUP_DELETE' => true,
			'TOPIC_SHOW' => true,
			'TOPIC_CREATE' => true,
			'TOPIC_UPDATE' => true,
			'TOPIC_DELETE' => true,
			'COMMENT_SHOW' => true,
			'COMMENT_CREATE' => true,
			'COMMENT_UPDATE' => true,
			'COMMENT_DELETE' => true,
		];
	}
}