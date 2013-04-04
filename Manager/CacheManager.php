<?php

namespace IMAG\SimpleCacheBundle\Manager;

class CacheManager
{
    private 
        $config,
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

    public function getReference($param)
    {
        return $this->storage->getCacheFile($param);
    }

    public function addReference($param)
    {
        $this->storage->setCacheFile($param);

        return $this;
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

        $time = new \Datetime('+'.$this->config['storage'][$storageMethod]['default_lifetime'].' seconds');
        $this->storage->setLifetime($time);

        return $this;
    }
}