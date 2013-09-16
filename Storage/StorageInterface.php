<?php

namespace IMAG\SimpleCacheBundle\Storage;

interface StorageInterface
{
    function getCacheFile($filename);
    function setCacheFile($filename, $param);
    function getLifetime();
    function setLifetime($time);
    function setExtrasParameters(array $configs);
    function getExtrasParameters();
    function cacheClear();
    function clearExpiredRef();
}