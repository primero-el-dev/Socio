<?php

namespace App\Entity\Trait;

use App\Entity\MediaObject;

trait MediaObjects
{
	public function getMediaObjects(): array
	{
		return $this->mediaObjects;
	}

	public function setMediaObjects(array $mediaObjects): static
	{
		$this->mediaObjects = $mediaObjects;

		return $this;
	}

	public function getMediaObjectsByType(string $type): array
	{
		return array_filter($this->mediaObjects, fn($mo) => $mo->getType($type));
	}

	public function addMediaObject(MediaObject $mediaObject): static
	{
		if (!$this->hasMediaObject($mediaObject)) {
			$this->mediaObjects[] = $mediaObject;
		}

		return $this;
	}

	public function removeMediaObject(MediaObject $mediaObject): static
	{
		$this->MediaObjects = array_filter(
			$this->mediaObjects, 
			fn($mo) => $mo->getId() === $mediaObject->getId()
		);

		return $this;
	}

	public function hasMediaObject(MediaObject $mediaObject): bool
	{
		return in_array(
			$mediaObject->getId(), 
			array_map(
				fn($mo) => $mo->getId(), 
				$this->mediaObjects
			)
		);
	}
}