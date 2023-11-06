<?php

namespace Signal\Cache\Adapters;

use DateInterval;
use Psr\Cache\CacheItemInterface;
use Signal\Cache\CacheInterface;
use Signal\Cache\CacheItem;
use Signal\Cache\Exceptions\InvalidCacheKeyException;
use Signal\Cache\Ttl;

abstract class AbstractAdapter implements CacheInterface
{
    /**
     * @var CacheItem[]
     */
    private array $deferred = [];

    abstract protected function getValue(string $key): mixed;

    abstract protected function setValue(string $key, mixed $value, DateInterval|int|null $ttl = null): bool;

    abstract protected function deleteValue(string $key): void;

    abstract protected function flush(): bool;

    abstract protected function keyExists(string $key): bool;

    public function get(string $key, mixed $default = null): mixed
    {
        return $this->has($key) ? $this->deserialize($this->getValue($key)) : $default;
    }

    public function has(string $key): bool
    {
        $this->validateKey($key);
        return $this->keyExists($key);
    }

    public function set(string $key, mixed $value, DateInterval|int|null $ttl = null): bool
    {
        $this->validateKey($key);

        if (is_null($ttl) || Ttl::secondsLeft($ttl) > 0) {
            return $this->setValue($key, $this->serialize($value), $ttl);
        }

        return false;
    }

    public function delete(string $key): bool
    {
        if (! $this->has($key)) {
            return false;
        }

        $this->deleteValue($key);
        return true;
    }

    public function deleteMultiple(iterable $keys): bool
    {
        if (empty($keys)) {
            return false;
        }

        $deleted = true;
        foreach ($keys as $key) {
            if (!$this->delete($key)) {
                $deleted = false;
            }
        }

        return $deleted;
    }

    public function getMultiple(iterable $keys, mixed $default = null): iterable
    {
        $values = [];
        foreach ($keys as $key) {
            $values[$key] = $this->has($key) ? $this->deserialize($this->getValue($key)) : $default;
        }

        return $values;
    }

    public function setMultiple(iterable $values, DateInterval|int|null $ttl = null): bool
    {
        if (Ttl::secondsLeft($ttl) < 0) {
            return false;
        }

        foreach ($values as $key => $value) {
            $this->validateKey($key);
            $this->setValue($key, $this->serialize($value), $ttl);
        }

        return true;
    }

    public function getItem(string $key): CacheItemInterface
    {
        return new CacheItem(
            key: $key,
            value: $this->has($key) ? self::get($key) : null
        );
    }

    public function getItems(array $keys = []): iterable
    {
        return array_map(fn($key) => $this->getItem($key), $keys);
    }

    public function hasItem(string $key): bool
    {
        return $this->has($key) && $this->getItem($key)->isHit();
    }

    public function clear(): bool
    {
        return $this->flush();
    }

    public function deleteItem(string $key): bool
    {
        return $this->delete($key);
    }

    public function deleteItems(array $keys): bool
    {
        return $this->deleteMultiple($keys);
    }

    public function save(CacheItemInterface $item): bool
    {
        if (!$item->hasExpired()) {
            return false;
        }

        return $this->set(
            $item->getKey(),
            $this->serialize($item->get()),
            $item->getExpiration()
        );
    }

    public function saveDeferred(CacheItemInterface $item): bool
    {
        $this->deferred[$item->getKey()] = $item;
        return true;
    }

    public function commit(): bool
    {
        foreach ($this->deferred as $item) {
            $this->save($item);
        }

        return true;
    }

    public function validateKey(string $key): void
    {
        if (! self::testKey($key)) {
            throw new InvalidCacheKeyException("Invalid key: $key");
        }
    }

    protected function deserialize(string $serialized): mixed
    {
        return unserialize($serialized);
    }

    protected function serialize(mixed $value): string
    {
        return serialize($value);
    }

    protected function testKey(string $key): bool
    {
        return false !== preg_match("/^[a-z0-9_.]{1,64}$/i", $key);
    }
}