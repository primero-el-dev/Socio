<?php

namespace App\Entity\Trait;

trait Configuration
{
	public function getConfiguration(): ?array
    {
        return $this->configuration;
    }

    public function setConfiguration(array $configuration): self
    {
        $this->configuration = $configuration;

        return $this;
    }

    public function setConfigurationKeys(
        array $keys, 
        $value, 
        ?array &$array = null
    ): self
    {
        if ($array === null) {
            $array = &$this->configuration;
        }

        if (!$keys) {
            return $this;
        }
        elseif (count($keys) === 1) {
            $array[$keys[0]] = $value;

            return $this;
        }
        else {
            $key = array_shift($keys);

            if (!isset($array[$key])) {
                $array[$key] = [];
            }

            return $this->setConfigurationKeys($keys, $value, $array[$key]);
        }
    }

    public function getConfigurationValue(array $keys)
    {
        $value = $this->configuration;

        foreach ($keys as $key) {
            if (!$value[$key]) {
                return null;
            }
            $value = $value[$key];
        }

        return $value;
    }
}