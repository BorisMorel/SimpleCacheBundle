<?php

namespace IMAG\SimpleCacheBundle\Storage;

class HddStorage extends AbstractStorage
{
    public function getCacheFile($ref)
    {
        $cacheFile = $this->getCacheFilePath($ref);

        if (false === file_exists($cacheFile)) {
            return false;
        }
        
        if (0 != $this->getLifetime()) {
            $stat = stat($cacheFile);
            if ($stat['ctime'] + $this->getLifetime() < time()) {
                unlink($cacheFile);
                
                return false;
            }
        }

        $handle = fopen($cacheFile, 'r');
        $content = fread($handle, filesize($cacheFile));
        fclose($handle);

        return unserialize($content);
    }

    public function setCacheFile($ref, $param)
    {
        $path = $this->getCacheFilePath($ref);
        
        $pathInfo = pathinfo($path);

        if (false === is_dir($pathInfo['dirname'])) {
            $this->createDirname($pathInfo['dirname']);
        }
        
        if (!$handle = @fopen($path, 'x')) {
            throw new \RuntimeException(sprintf('File "%s" can\'t be open for writing', $cacheFile));
        }
        
        fwrite($handle, serialize($param));
        fclose($handle);
        
        return $this;
    }

    public function cacheClear()
    {
        $it = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($this->getPrependPath(), \RecursiveDirectoryIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::CHILD_FIRST
        );

        foreach ($it as $splFileInfo) {
            $fct = $splFileInfo->isDir() ? 'rmdir' : 'unlink';

            call_user_func($fct, $splFileInfo->getRealPath());
        }
    }

    private function getCacheFilePath($ref)
    {
        $hash = $this->getRefHash($ref);
        $folders = $this->splitHash($hash);
        
        $path =
            $this->getPrependPath()
            .DIRECTORY_SEPARATOR
            .implode(DIRECTORY_SEPARATOR, $folders)
            .DIRECTORY_SEPARATOR
            .$ref
            .'.'
            .$this->getFileExtension()
            ;
        
        return $path;
    }

    private function getRefHash($ref)
    {
        return md5($ref);
    }

    private function splitHash($hash)
    {
        $maxLength = $this->getFolderLength() * $this->getDeep();
        
        if ($maxLength >= mb_strlen($hash)) {
            throw new \RuntimeException("Split hash can't be performed due to a invalid folder length and deep");
        }
        
        $folderPart = mb_substr($hash, 0, $maxLength);

        $folders = str_split($folderPart, $this->getFolderLength());

        return $folders;
    }
 
    private function getFolderLength()
    {
        $params = $this->getExtrasParameters();

        return isset($params['folder_length']) ? (int)$params['folder_length'] : 3;
    }

    private function getDeep()
    {
        $params = $this->getExtrasParameters();

        return isset($params['deep']) ? (int)$params['deep'] : 3;
    }

    private function getBaseDir()
    {
        return isset($this->configs['base_dir']) ? $this->configs['base_dir'] : '/tmp';
    }

    private function getCacheFolder()
    {
        return isset($this->configs['cache_folder']) ? $this->configs['cache_folder'] : 'cache_T05uXu50';
    }

    private function getFileExtension()
    {
        return isset($this->configs['file_extension']) ? $this->configs['file_extension'] : 'imag.cache';
    }

    private function createDirname($dir)
    {
        if (false === mkdir($dir, 0755, true)) {
            throw new \RuntimeException(sprintf("Unable to create %s", $dir));
        }

        return true;
    }

    private function getPrependPath()
    {
        return 
            $this->getBaseDir()
            .DIRECTORY_SEPARATOR
            .$this->getCacheFolder()
            ;
    }
}