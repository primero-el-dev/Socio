<?php

namespace App\Entity\Interface;

use App\Entity\MediaObject;

interface HasMediaObjects
{
	public function getMediaObjects(): array;

	public function setMediaObjects(array $mediaObjects): static;

	public function getMediaObjectsByType(string $type): array;

	public function addMediaObject(MediaObject $mediaObject): static;

	public function removeMediaObject(MediaObject $mediaObject): static;

	public function hasMediaObject(MediaObject $mediaObject): bool;
}