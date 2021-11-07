<?php

namespace App\Util;

use App\Entity\Entity;

class EntityUtils
{
	public static function areSame(?Entity $first, ?Entity $second): bool
	{
		return (!$first && !$second) || ($first && $second && $first->getId() === $second->getId());
	}
}
