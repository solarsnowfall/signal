<?php

namespace Cache;

use Signal\Cache\Adapters\FileCache;
use Signal\Cache\CacheInterface;
use Signal\Filesystem\Adapters\DiskFilesystem;
use Signal\Tests\Cache\CacheTestAbstract;

class FileCacheTest extends CacheTestAbstract
{
    const CACHE_DIRECTORY = 'tests/Cache/cache';

    public function extensionAvailable(): array
    {
        return [true, null];
    }

    public function getAdaptor(): CacheInterface
    {
        return new FileCache(
            new DiskFilesystem(self::CACHE_DIRECTORY)
        );
    }

    public function tearDown(): void
    {
        parent::tearDown();
        $dirname = dirname(self::CACHE_DIRECTORY);
        $path = substr(self::CACHE_DIRECTORY, strlen($dirname) + 1);
        $filesystem = new DiskFilesystem($dirname);
        $filesystem->deleteDirectory($path);
    }
}