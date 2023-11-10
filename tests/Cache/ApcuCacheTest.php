<?php

namespace Cache;

use Signal\Cache\Adapters\ApcuCache;
use Signal\Cache\CacheInterface;
use Signal\Tests\Cache\CacheTestAbstract;

class ApcuCacheTest extends CacheTestAbstract
{

    public function extensionAvailable(): array
    {
        return [function_exists('apcu_exists'), 'apcu'];
    }

    public function getAdaptor(): CacheInterface
    {
        return new ApcuCache();
    }

    public function tearDown(): void
    {
        parent::tearDown();
        apcu_clear_cache();
    }
}