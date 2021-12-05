<?php

namespace App\Entity\Interface;

interface HasConfiguration
{
	public function getConfiguration(): ?array;

    public function setConfiguration(array $configuration): self;

    public function setConfigurationKeys(
        array $keys, 
        $value, 
        ?array &$array = null
    ): self;

    public function getConfigurationValue(array $keys);
}