<?php

namespace Signal\Cache\Adapters;

use DateInterval;
use Signal\Cache\Ttl;

class RuntimeCache extends AbstractAdapter
{
    protected array $cache = [];

    protected array $expirations = [];

    protected array $deferred = [];

    protected function setValue(string $key, mixed $value, DateInterval|int|null $ttl = null): bool
    {
        $this->cache[$key] = $this->serialize($value);
        $this->expirations[$key] = Ttl::secondsLeft($ttl);
        return true;
    }

    protected function deleteValue(string $key): void
    {
        unset($this->cache[$key], $this->expirations[$key], $this->deferred[$key]);
    }

    protected function keyExists(string $key): bool
    {
        if (array_key_exists($key, $this->cache)) {
            $expiration = $this->expirations[$key];

            if (null !== $expiration && $expiration < time()) {
                $this->deleteValue($key);
                return false;
            }

            return true;
        }

        return false;
    }

    protected function getValue(string $key): mixed
    {
        return $this->cache[$key];
    }

    protected function flush(): bool
    {
        $this->cache = $this->expirations = $this->deferred = [];
        return true;
    }

    public function deleteItems(array $keys): bool
    {
        return $this->deleteMultiple($keys);
    }
}