<?php

namespace App\Entity\Trait;

use Doctrine\ORM\Mapping as ORM;

trait Update
{
	/**
	 * @ORM\Column(type="datetimetz", nullable=true)
	 */
	protected ?\DateTimeInterface $updatedAt;

	public function getUpdatedAt(): ?\DateTimeInterface
	{
		return $this->updatedAt;
	}

	public function setUpdatedAt(\DateTimeInterface $updatedAt): self
	{
		$this->updatedAt = $updatedAt;

		return $this;
	}

	/**
	 * @ORM\PreUpdate
	 */
	public function setUpdatedAtToNow(): self
	{
		return $this->setupdatedAt(new \DateTimeImmutable());
	}
}