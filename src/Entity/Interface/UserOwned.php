<?php

namespace App\Entity\Interface;

use App\Entity\User;

interface UserOwned
{
	public function getAuthor(): ?User;

	public function setAuthor(?User $author): self;
}