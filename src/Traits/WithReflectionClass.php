<?php

namespace Signal\Traits;

use ReflectionClass;
use Signal\Reflection\ReflectionFactory;

trait WithReflectionClass
{
    public static function withClass(object|string $objectOrClass): static
    {
        return new static(
            $objectOrClass instanceof ReflectionClass
                ? $objectOrClass
                : ReflectionFactory::getClass($objectOrClass)
        );
    }
}