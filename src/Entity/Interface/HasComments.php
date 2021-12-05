<?php

namespace App\Entity\Interface;

use App\Entity\Comment;
use Doctrine\Common\Collections\Collection;

interface HasComments
{
	public function getComments(): Collection;

	public function setComments(Collection $comments): self;

	public function addComment(Comment $comment): self;

	public function removeComment(Comment $comment): self;
}