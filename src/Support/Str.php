<?php

namespace Signal\Support;

use BadMethodCallException;
use Closure;
use http\Exception\RuntimeException;

/**
 * @method static string camel(string $value)
 * @method static string kebab(string $value, string $delimiter = '-')
 * @method static string snake(string $value, string $delimiter = '_')
 * @method static string studly(string $value)
 */
class Str
{
    private static array $cache = [];

    public static function __callStatic(string $name, array $arguments)
    {
        $key = static::cacheKey($name, ...$arguments);

        if (array_key_exists($key, static::$cache)) {
            return static::$cache[$key];
        }

        return static::$cache[$key] = self::getMethod($name)(...$arguments);
    }

    private static function getMethod(string $name): Closure
    {
        return match($name) {
            'camel' => fn(string $value) => lcfirst(static::studly($value)),
            'kebab' => fn(string $value, string $delimiter = '-') => static::snake($value, $delimiter),
            'snake' => fn(string $value, string $delimiter = '_') => ctype_lower($value)
                ? $value
                : strtolower(preg_replace(
                    pattern: '/(.)(?=[A-Z])/u',
                    replacement: '$1'.$delimiter,
                    subject: preg_replace(
                        pattern: '/\s+/u',
                        replacement: '',
                        subject: ucwords($value)
                    )
                )),
            'studly' => fn(string $value) => implode(
                array_map(
                    callback: fn($word) => ucfirst($word),
                    array: explode(' ', str_replace(['-', '_'], ' ', $value))
                )
            ),
            default => throw new BadMethodCallException(
                message: 'Call to undefined method ' . static::class . '::' . $name
            )
        };
    }

    private static function cacheKey(...$arguments): string
    {
        return md5(implode('-', $arguments));
    }
}