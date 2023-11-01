<?php

namespace Signal\Reflection;

use InvalidArgumentException;
use ReflectionClass;
use ReflectionException;
use ReflectionMethod;
use ReflectionProperty;
use RuntimeException;

class ReflectionFactory
{
    private static array $cache = [];

    public static function className(object|string $objectOrClass): string
    {
        return is_object($objectOrClass) ? get_class($objectOrClass) : $objectOrClass;
    }

    public static function classShortName(object|string $objectOrClass): string
    {
        $class = static::className($objectOrClass);

        if (!isset(static::$cache['class-short-name'][$class])) {
            static::$cache['class-short-name'][$class] = static::getClass($objectOrClass)->getShortName();
        }

        return static::$cache['class-short-name'][$class];
    }

    public static function getClass(object|string $objectOrClass): ReflectionClass
    {
        try {
            return new ReflectionClass($objectOrClass);
        } catch (ReflectionException $exception) {
            $class = static::className($objectOrClass);
            throw new RuntimeException(message: "Class not found: $class", previous: $exception);
        }
    }

    public static function getClassInstance(object|string $objectOrClass, array $arguments = []): object
    {
        try {
            return self::getClass($objectOrClass)->newInstance(...$arguments);
        } catch (ReflectionException $exception) {
            $class = static::className($objectOrClass);
            throw new InvalidArgumentException(message: "Unable to create object for: $class", previous: $exception);
        }
    }

    public static function getMethod(object|string $objectOrClass, string $method): ReflectionMethod
    {
        try {
            return new ReflectionMethod($objectOrClass, $method);
        } catch (ReflectionException $exception) {
            $class = static::className($objectOrClass);
            throw new RuntimeException(message: "Method not found: $class::$method", previous: $exception);
        }
    }

    public static function getProperty(object|string $objectOrClass, string $property): ReflectionProperty
    {
        try {
            return new ReflectionProperty($objectOrClass, $property);
        } catch (ReflectionException $exception) {
            $class = static::className($objectOrClass);
            throw new RuntimeException(message: "Property not found: $class::$property", previous: $exception);
        }
    }
}