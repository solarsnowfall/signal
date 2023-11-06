<?php

namespace Signal\Cache;

use DateInterval;
use DateTime;
use DateTimeInterface;
use Psr\Cache\CacheItemInterface;

class CacheItem implements CacheItemInterface
{
    public function __construct(
        private readonly string $key,
        private mixed $value,
        private ?DateTimeInterface $expiration = null
    ) {}

    public function getKey(): string
    {
        return $this->key;
    }

    public function get(): mixed
    {
        return $this->value;
    }

    public function getExpiration(): ?DateTimeInterface
    {
        return $this->expiration;
    }

    public function isHit(): bool
    {
        return !is_null($this->value) && !$this->hasExpired();
    }

    public function set(mixed $value): static
    {
        $this->value = $value;
        return $this;
    }

    public function expiresAt(?DateTimeInterface $expiration): static
    {
        $this->expiration = $expiration;
        return $this;
    }

    public function expiresAfter(DateInterval|int|null $time): static
    {
        $this->expiration = (new DateTime)->add(Ttl::dateInterval($time));
        return $this;
    }

    public function hasExpired(): bool
    {
        if (is_null($this->expiration)) {
            return false;
        }

        return $this->expiration->getTimestamp() > (new DateTime)->getTimestamp();
    }
}