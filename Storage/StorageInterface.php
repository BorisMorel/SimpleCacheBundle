<?php

namespace IMAG\SimpleCacheBundle\Storage;

interface StorageInterface
{
    function getUniqueName($param);
    function getCacheFile($param);
    function setCacheFile($param);
    function getLifetime();
    function setLifetime(\Datetime $time);
    function setExtrasParameters(array $configs);
}