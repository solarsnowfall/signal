<?php

namespace Signal\Tests\Cache;

use Signal\Cache\Adapters\RuntimeCache;
use Signal\Cache\CacheInterface;

class RuntimeCacheTest extends CacheTestAbstract
{

    public function extensionAvailable(): array
    {
        return [true, null];
    }

    public function getAdaptor(): CacheInterface
    {
        return new RuntimeCache();
    }
}