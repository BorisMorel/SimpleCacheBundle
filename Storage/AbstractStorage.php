<?php

namespace IMAG\SimpleCacheBundle\Storage;

abstract class AbstractStorage implements StorageInterface
{
    protected $configs;

    public function setLifetime($time)
    {
        $this->lifetime = $time;

        return $this;
    }

    public function getLifetime()
    {
        return $this->lifetime;
    }

    public function setExtrasParameters(array $configs)
    {
        $this->configs = $configs;

        return $this;
    }

    public function getExtrasParameters()
    {
        return $this->configs;
    }
}
