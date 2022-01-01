<?php

namespace App\Entity\Trait;

use App\Entity\User;
use Doctrine\ORM\Mapping as ORM;

trait UserOwned
{
	public function getAuthor(): ?User
	{
		return $this->author;
	}

	public function setAuthor(?User $author): self
	{
		$this->author = $author;

		return $this;
	}
}