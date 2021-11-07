<?php

namespace App\Entity;

use App\Entity\Dictionary;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ORM\RelationRepository")
 * @ORM\Table(name="dictionary")
 */
class Relation extends Dictionary
{
	
}