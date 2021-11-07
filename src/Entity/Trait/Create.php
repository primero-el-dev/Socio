<?php

namespace App\Entity\Trait;

use Doctrine\ORM\Mapping as ORM;

trait Create
{
	/**
	 * @ORM\Column(type="datetimetz_immutable")
	 */
	protected ?\DateTimeInterface $createdAt;

	public function getCreatedAt(): ?\DateTimeInterface
	{
		return $this->createdAt;
	}

	public function setCreatedAt(\DateTimeInterface $createdAt): self
	{
		$this->createdAt = $createdAt;

		return $this;
	}

	/**
	 * @ORM\PrePersist
	 */
	public function setCreatedAtToNow(): self
	{
		return $this->setCreatedAt(new \DateTimeImmutable());
	}
}