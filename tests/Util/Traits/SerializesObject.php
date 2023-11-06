<?php

namespace Signal\Tests\Util\Traits;

trait SerializesObject
{
    public function __serialize(): array
    {
        return get_object_vars($this);
    }

    public function __unserialize(array $data): void
    {
        foreach (array_keys(get_object_vars($this)) as $key) {
            if (isset($data[$key])) {
                $this->$key = $data[$key];
            }
        }
    }
}