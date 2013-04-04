<?php

namespace IMAG\SimpleCacheBundle\Storage;

class HddStorage extends AbstractStorage
{
    public function getCacheFile($filename)
    {
        $cacheFile = $this->getCacheFilename($filename);

        if (!file_exists($cacheFile)) {
            return false;
        }
        
        $stat = stat($cacheFile);
        if ($stat['ctime'] + $this->getLifetime() < time()) {
            unlink($cacheFile);
            
            return false;
        }

        $handle = fopen($cacheFile, 'r');
        $content = fread($handle, filesize($cacheFile));
        fclose($handle);

        return unserialize($content);
    }

    public function setCacheFile($filename, $param)
    {
        $cacheFile = $this->getCacheFilename($filename);

        if (!$handle = @fopen($cacheFile, 'x')) {
            throw new \RuntimeException(sprintf('File "%s" can\'t be open for writing', $cacheFile));
        }

        fwrite($handle, serialize($param));
        fclose($handle);

        return $this;
    }

    public function cacheClear()
    {
        $cacheDir = $this->getCacheDir();
        $fileExtension = $this->getFileExtension();

        $pattern = $cacheDir
            .DIRECTORY_SEPARATOR
            .'*'
            .'.'
            .$fileExtension
            ;

        foreach(glob($pattern) as $filename) {
            unlink($filename);
        }

        return true;
    }

    private function getCacheFilename($filename)
    {
        $cacheDir = $this->getCacheDir();
        $fileExtension = $this->getFileExtension();

        $file = $cacheDir
            .DIRECTORY_SEPARATOR
            .$filename
            .'.'
            .$fileExtension
            ;

        return $file;
    }

    private function getCacheDir()
    {
        return isset($this->configs['cache_dir']) ? $this->configs['cache_dir'] : '/tmp';
    }

    private function getFileExtension()
    {
        return isset($this->configs['file_extension']) ? $this->configs['file_extension'] : 'bomo.cache';
    }
}