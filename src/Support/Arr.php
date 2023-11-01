<?php

namespace Signal\Support;

class Arr
{
    public static function isSequential(array $array): bool
    {
        return $array === [] || range(0, count($array) - 1) === array_keys($array);
    }

    public static function first(array $array, callable $callback = null, mixed $default = null): mixed
    {
        if (empty($array)) {
            return $default;
        }

        $key = array_key_first($array);

        return is_null($callback) ? $array[$key] : $callback($array[$key], $key);
    }

    public static function last(array $array, callable $callback = null, mixed $default = null): mixed
    {
        if (empty($array)) {
            return $default;
        }

        $key = array_key_last($array);

        return is_null($callback) ? $array[$key] : $callback($array[$key], $key);
    }
}