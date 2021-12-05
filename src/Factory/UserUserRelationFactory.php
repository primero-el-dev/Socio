<?php

namespace App\Factory;

use App\Entity\User;
use App\Entity\UserUserRelation;
use App\Factory\Factory;
use Doctrine\ORM\EntityManagerInterface;

class UserUserRelationFactory implements Factory
{
	public static function createDenyActionForUserWhereSubject(
		string $action, 
		?User $user = null, 
		User $subject
	): UserUserRelation
	{
		$relation = new UserUserRelation();
		$relation->setAction($action);
		$relation->setWho($user);
		$relation->setWhom($subject);
		$relation->setDenied(true);

		return $relation;
	}
}