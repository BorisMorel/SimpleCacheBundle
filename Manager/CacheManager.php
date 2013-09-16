<?php

namespace IMAG\SimpleCacheBundle\Manager;

class CacheManager
{
    private 
        $config,
        $refKey,
        $storage
        ;

    public function __construct($config)
    {
        $this->config = $config;

        try {
            $this->storage = $this->getStorageClass();
            $this
                ->setLifetime()
                ->setExtrasParameters()
                ;

        } catch(\InvalidArgumentException $e) {
            throw $e;

        }
    }

    public function getReference($param = null)
    {        
        $key = $this->getUniqueKey($param);

        return $this->storage->getCacheFile($key);
    }

    public function addReference($param)
    {
        $key = $this->getUniqueKey($param);

        $this->storage->setCacheFile($key, $param);
        $this->refKey = null;

        return $this;
    }

    public function setReferenceKey($key)
    {
        $this->refKey = $key;

        return $this;
    }

    public function getReferenceKey()
    {
        return $this->refKey;
    }

    public function clearCache()
    {
        return $this->storage->cacheClear();
    }

    public function clearExpired()
    {
        return $this->storage->clearExpiredRef();
    }

    private function getUniqueKey($param = null)
    {
        if (null === $param && null === $this->refKey) {
            throw \RuntimeException("You can't call *Reference method without a key");
        }

        return null === $this->refKey ? $this->getUniqueName($param) : $this->refKey;
    }

    private function getUniqueName($param)
    {
        return md5(serialize($param));
    }

    private function getStorageClass()
    {
        $storageMethod = $this->config['storage_method'];
        
        if (!$className = $this->config['storage'][$storageMethod]['class']) {
            throw new \InvalidArgumentException(sprintf(
                'The storage method %s need to be defined in the config.yml',
                $storageMethod
            ));
        }
        
        if (!class_exists($className)) {
            throw new \InvalidArgumentException(sprintf(
                'Class name %s need to be defined for storage : %s',
                $className,
                $storageMethod
            ));
        }
        
        return new $className();
    }

    private function setExtrasParameters()
    {
        $storageMethod = $this->config['storage_method'];

        if ($extras = $this->config['storage'][$storageMethod]['extras']) {
            $this->storage->setExtrasParameters($extras);
        }
        
        return $this;
    }

    private function setLifetime()
    {
        $storageMethod = $this->config['storage_method'];

        $time = $this->config['storage'][$storageMethod]['default_lifetime'];
        $this->storage->setLifetime($time);

        return $this;
    }
}