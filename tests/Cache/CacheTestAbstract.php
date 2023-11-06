<?php

namespace Signal\Tests\Cache;

use PHPUnit\Framework\TestCase;
use Signal\Cache\CacheInterface;
use Signal\Cache\Exceptions\InvalidCacheKeyException;

abstract class CacheTestAbstract extends TestCase
{
    protected CacheInterface $cache;

    abstract public function extensionAvailable(): array;

    abstract public function getAdaptor(): CacheInterface;

    protected function setUp(): void
    {
        parent::setUp();

        $test = $this->extensionAvailable();

        if (!$test[0]) {
            $this->markTestSkipped("Extension '{$test[1]} not available");
        }

        $this->cache = $this->getAdaptor();
    }

    public function testSet()
    {
        $this->assertTrue($this->cache->set('valid_key', 'value'));
    }

    public function testGet()
    {
        $this->cache->set('test', 'value');
        $this->assertEquals('value', $this->cache->get('test'));
    }

    public function testGetWithDefault()
    {
        $this->assertEquals('default', $this->cache->get('test', 'default'));
    }

    public function testSetWithBadKey()
    {
        try {
            $this->cache->set('!@#$%', 'test');
        } catch (\Throwable $throwable) {
            $this->assertInstanceOf(InvalidCacheKeyException::class, $throwable);
        }
    }
}