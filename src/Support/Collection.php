<?php

namespace Signal\Support;

use ArrayAccess;
use ArrayIterator;
use IteratorAggregate;
use Traversable;

class Collection implements ArrayAccess, IteratorAggregate
{
    public function __construct(
        protected iterable $items = []
    ) {}

    public static function for(iterable $items): static
    {
        return new static($items);
    }

    public function add(mixed $item): static
    {
        $this->items[] = $item;
        return $this;
    }

    public function all(): array
    {
        return $this->items;
    }

    public function count(): int
    {
        return count($this->items);
    }

    public function filter(callable $callback): static
    {
        return new static(array_filter($this->items, $callback));
    }

    public function first(callable $callback = null, mixed $default = null): mixed
    {
        return Arr::first($this->items, $callback, $default);
    }

    public function forget(int|array $keys): static
    {
        foreach ((array) $keys as $key) {
            unset($this->items[$key]);
        }

        return $this;
    }

    public function get(int $key, mixed $default = null): mixed
    {
        return array_key_exists($key, $this->items) ? $this->items[$key] : $default;
    }

    public function last(callable $callback = null, mixed $default = null): mixed
    {
        return Arr::last($this->items, $callback, $default);
    }

    public function map(callable $callback): static
    {
        return new static(array_map($callback, $this->items));
    }

    public function toArray(): array
    {
        return (array) $this->items;
    }

    public function offsetExists(mixed $offset): bool
    {
        return isset($this->items[$offset]);
    }

    public function offsetGet(mixed $offset): mixed
    {
        return $this->items[$offset];
    }

    public function offsetSet(mixed $offset, mixed $value): void
    {
        if (is_null($offset)) {
            $this->items[] = $value;
        } else {
            $this->items[$offset] = $value;
        }
    }

    public function offsetUnset(mixed $offset): void
    {
        unset($this->items[$offset]);
    }

    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->items);
    }
}