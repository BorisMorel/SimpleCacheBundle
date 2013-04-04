<?php

namespace IMAG\SimpleCacheBundle\Storage;

abstract class AbstractStorage implements StorageInterface
{
    public function getUniqueName($param)
    {
        return md5(serialize($param));
    }

    public function setLifetime(\Datetime $time)
    {
        $this->lifetime = $time;

        return $this;
    }

    public function getLifetime()
    {
        return $this->lifetime;
    }
}
