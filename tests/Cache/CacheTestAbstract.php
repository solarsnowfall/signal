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

    public function testHas()
    {
        $this->assertFalse($this->cache->has('test'));
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

    public function testSetMultiple()
    {
        $values = [
            'test1' => 'value1',
            'test2' => 'value2'
        ];

        $this->cache->setMultiple($values);
        $this->assertEquals('value1', $this->cache->get('test1'));
        $this->assertEquals('value2', $this->cache->get('test2'));
    }

    public function testGetMultiple()
    {
        $values = [
            'test1' => 'value1',
            'test2' => 'value2'
        ];

        $this->cache->setMultiple($values);
        $found = $this->cache->getMultiple(['test1', 'test2']);
        $this->assertEquals($values['test1'], $found['test1']);
        $this->assertEquals($values['test2'], $found['test2']);
    }

    public function testGetWithDefault()
    {
        $this->assertEquals('default', $this->cache->get('test', 'default'));
    }

    public function testGetWithExpiration()
    {
        $ttl = new \DateInterval('PT1S');
        $this->cache->set('test', 'value', $ttl);
        $this->assertEquals('value', $this->cache->get('test'));
        sleep(2);
        $this->assertNull($this->cache->get('test'));
    }

    public function testSetWithBadKey()
    {
        try {
            $this->cache->set('(invalid)', 'test');
        } catch (\Throwable $throwable) {
            $this->assertInstanceOf(InvalidCacheKeyException::class, $throwable);
        }
    }

    public function testDelete()
    {
        $this->cache->set('test', 'value');
        $this->assertTrue($this->cache->delete('test'));
        $this->assertNull($this->cache->get('test'));
    }

    public function testDeleteMultiple()
    {
        $this->cache->setMultiple(['test1' => 'value1', 'test2' => 'value2']);
        $this->cache->deleteMultiple(['test1', 'test2']);
        $found = $this->cache->getMultiple(['test1', 'test2']);
        $this->assertNull($found['test1']);
        $this->assertNull($found['test2']);
    }

    public function testClear()
    {
        $this->cache->set('test', 'value');
        $this->cache->clear();
        $this->assertNull($this->cache->get('test'));
    }
}