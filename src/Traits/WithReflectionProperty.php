<?php

namespace Signal\Traits;

use Signal\Reflection\ReflectionFactory;

trait WithReflectionProperty
{
    public static function withProperty(object|string $objectOrClass, string $property = null): static
    {
        return new static(
            $objectOrClass instanceof \ReflectionProperty
                ? $objectOrClass
                : ReflectionFactory::getProperty($objectOrClass, $property)
        );
    }
}