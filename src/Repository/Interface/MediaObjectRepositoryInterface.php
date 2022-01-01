<?php

namespace App\Repository\Interface;

use App\Entity\Interface\HasMediaObjects;
use Doctrine\Persistence\ObjectRepository;

interface MediaObjectRepositoryInterface extends ObjectRepository
{
	public function findByOwner(HasMediaObjects $object): array;
}