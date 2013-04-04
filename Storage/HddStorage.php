<?php

namespace IMAG\SimpleCacheBundle\Storage;

class HddStorage extends AbstractStorage
{
    private $configs;

    public function getCacheFile($param)
    {
        die('ici');
        $cacheFile = $this->getFileName($param);

        if (!file_exists($cacheFile)) {
            return false;
        }
        
        $stat = stat($cacheFile);
        if ($stat['ctime'] > $this->getLifetime()) {
            unlink($cacheFile);
            
            return false;
        }

        $handle = fopen($cacheFile, 'r');
        $content = fread($handle, filesize($cacheFile));
        fclose($handle);
        return unserialize($content);
    }

    public function setCacheFile($param)
    {
        $cacheFile = $this->getFileName($param);
        
        $handle = fopen($cacheFile, 'w+');
        fwrite($handle, serialize($param));
        fclose($handle);

        return $this;
    }

    public function setExtrasParameters(array $configs)
    {
        $this->configs = $configs;

        return $this;
    }

    private function getFileName($param)
    {
        $uniqueName = $this->getUniqueName($param);

        $cacheDir = isset($this->configs['cache_dir']) ? $this->configs['cache_dir'] : '/tmp';
        $fileExtension = isset($this->configs['file_extension']) ? $this->configs['file_extension'] : 'cache';

        $cacheFile = $cacheDir
            .DIRECTORY_SEPARATOR
            .$uniqueName
            .'.'
            .$fileExtension
            ;

        return $cacheFile;
    }
}