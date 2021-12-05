<?php

namespace App\Entity\Trait;

use Doctrine\ORM\Mapping as ORM;

trait SoftDelete
{
	/**
	 * @ORM\Column(type="datetime_immutable", nullable=true, options={"default":null})
	 */
	protected ?\DateTimeInterface $deletedAt;

    public function setDeletedAt(\DateTimeInterface $deletedAt = null)
    {
        $this->deletedAt = $deletedAt;

        return $this;
    }

    public function getDeletedAt()
    {
        return $this->deletedAt;
    }

    public function isDeleted()
    {
        return null !== $this->deletedAt;
    }
}