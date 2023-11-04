<?php

namespace Signal\Traits;

use Signal\Reflection\ReflectionFactory;

trait WithReflectionMethod
{
    public static function withMethod(object|string $objetOrClass, string $method = null): static
    {
        return new static(
            $objetOrClass instanceof \ReflectionMethod
                ? $objetOrClass
                : ReflectionFactory::getMethod($objetOrClass, $method)
        );
    }
}