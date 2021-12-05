<?php

namespace App\Security;

use App\Entity\Entity;

class Roles
{
	// Types
	public const TYPES = [
		'READ',
		'CREATE',
		'UPDATE',
		'DELETE',
	];

	public const ROLES = [
		// Groups
		'ROLE_READ_GROUP',
		'ROLE_CREATE_GROUP',
		'ROLE_UPDATE_GROUP',
		'ROLE_DELETE_GROUP',

		// Threads
		'ROLE_READ_THREAD',
		'ROLE_CREATE_THREAD',
		'ROLE_UPDATE_THREAD',
		'ROLE_DELETE_THREAD',

		// Posts
		'ROLE_READ_POST',
		'ROLE_CREATE_POST',
		'ROLE_UPDATE_POST',
		'ROLE_DELETE_POST',

		// Comments
		'ROLE_READ_COMMENT',
		'ROLE_CREATE_COMMENT',
		'ROLE_UPDATE_COMMENT',
		'ROLE_DELETE_COMMENT',
	];

	public static function getDefaultForUser(): array
	{
		return [
            'ROLE_USER',
            'ROLE_READ_GROUP',
            'ROLE_CREATE_GROUP',
            'ROLE_READ_THREAD',
            'ROLE_CREATE_THREAD',
            'ROLE_READ_POST',
            'ROLE_CREATE_POST',
            'ROLE_READ_COMMENT',
            'ROLE_CREATE_COMMENT',
        ];
	}

	public static function getRoleForTypeAndEntity(string $type, Entity $entity): ?string
	{
		$className = (new \ReflectionClass($entity))->getShortName();
		$className = strtoupper(preg_replace('/(?<!^)[A-Z]/', '_$0', $className));

		return self::getRoleForTypeAndClass($type, $className);
	}

	public static function getRoleForTypeAndClass(string $type, string $class): ?string
	{
		if (!in_array($type, self::TYPES, true)) {
			return null;
		}

		$roleName = sprintf('ROLE_%s_%s', $type, strtoupper($class));

		return in_array($roleName, self::ROLES, true) ? $roleName : null;
	}
}