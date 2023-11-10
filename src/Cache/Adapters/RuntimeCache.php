<?php

namespace Signal\Cache\Adapters;

use DateInterval;
use Signal\Cache\Ttl;

class RuntimeCache extends AbstractAdapter
{
    private array $values = [];
    private array $expirations = [];

    protected function serializedStorage(): bool
    {
        return true;
    }

    protected function getValue(string $key, mixed $default = null): mixed
    {
        return $this->values[$key] ?? $default;
    }

    protected function setValue(string $key, mixed $value, DateInterval|int|null $ttl = null): bool
    {
        $this->values[$key] = $value;
        $this->expirations[$key] = Ttl::timestamp($ttl);

        return true;
    }

    protected function deleteValue(string $key): bool
    {
        unset($this->values[$key], $this->expirations[$key], $this->deferred[$key]);

        return true;
    }

    protected function flush(): bool
    {
        $this->values = $this->expirations = [];

        return true;
    }

    protected function keyExists(string $key): bool
    {
        if (array_key_exists($key, $this->values)) {
            return !Ttl::expired($this->expirations[$key]);
        }

        return false;
    }
}