# SimpleCacheBundle

It provide a data caching for many ressources. It can be easly extended with an other storage method.

## Contact

Nick: aways
IRC: irc.freenode.net - #symfony-fr

## Install

1. Download with composer
2. Enable the Bundle
3. Configure
4. Use the bundle
5. Example

### Composer
Add SimpleCacheBundle in your project's `composer.json`

```json
{
    "require": {
        "imag/simple-cache-bundle": "dev-master"
    }
}
```

### Enable the Bundle

``` php
<?php
// app/AppKernel.php

public function registerBundles()
{
    $bundles = array(
        // ...
        new IMAG\LdapBundle\IMAGSimpleCacheBundle(),
    );
}
```

### Configure

``` yml
#config.yml
imag_simple_cache:
    storage_method: hdd
    storage:
        hdd:
            class: \IMAG\SimpleCacheBundle\Storage\HddStorage
            default_lifetime: 3600
            extras:
                base_dir: /tmp
                cache_folder: cache_q1hU5HHt
                file_extension: imag.cache
                deep: 3
                folder_length: 3


```
### Console

``` bash
php app/console help simplecache:cache-clear
Usage:
 simplecache:cache-clear [--entire-cache]
```

By default, clear-cache without any parameter clear expired references only.

### Use the bundle

#### Inject:

``` xml
<service id="foo_bar.service" class="%foo_bar.service.class%">
    <argument type="service" id="imag_simple_cache.cache_manager" />
</service>
```

#### Methods

``` 
mixed      getReference(mixed $param)
this       addReference(mixed $param)
this       setReferenceKey(string $key)
string     getReferenceKey()
array      clearCache()
array      clearExpired()
```

**Note:**

> If you don't use setReferenceKey($param) method the storage reference key is calculate with a simple process: 
> md5(serialize($param))

### Example

``` php
public function __construct(\IMAG\SimpleCacheBundle\Manager\CacheManager $cache)
{
    $this->cache = $cache;
}

public function getArchiveOfAppsPdf(array $applications)
{
    $key = md5(serialize($applications));

    $this->cache->setReferenceKey($key);
    
     // Because setReference have been called, this reference is searched with the key $key
    if ($cached = $this->cache->getReference()) {
        return $cached;
    }

    $zip = $this->zip->getZip();

    // Because setReference have been called, this method use $key to store the $zip reference
    $this->cache->addReference($zip);

    return $zip;
}

public function getExample(array $applications)
{
    $zip = $this->getZip($applications);

    // This reference is searched under md5(serialize($zip))
    if ($cached = $this->cache->getReference($zip)) {
        return $cached;
    }

    // This reference is stored under md5(serialize($zip))
    $this->cache->addReference($zip);

    return $zip;
}
```
