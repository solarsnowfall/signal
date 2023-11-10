<?php

namespace Signal\Cache\Adapters;

use DateInterval;
use Signal\Cache\Ttl;

class ApcuCache extends AbstractAdapter
{

    protected function serializedStorage(): bool
    {
        return true;
    }

    protected function getValue(string $key, mixed $default = null): mixed
    {
        $value = apcu_fetch($key, $success);

        return $success ? $value : $default;
    }

    protected function setValue(string $key, mixed $value, DateInterval|int|null $ttl = null): bool
    {
        return apcu_store($key, $value, Ttl::secondsLeft($ttl));
    }

    protected function deleteValue(string $key): bool
    {
        return apcu_delete($key);
    }

    protected function flush(): bool
    {
        return apcu_clear_cache();
    }

    protected function keyExists(string $key): bool
    {
        return apcu_exists($key);
    }
}