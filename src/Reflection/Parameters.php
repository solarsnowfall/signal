<?php

namespace Signal\Reflection;

use ReflectionMethod;
use ReflectionParameter;

class Parameters
{
    public function __construct(
        private readonly ReflectionMethod $method
    ) {}

    public static function for(object|string $objectOrClass, string $method): static
    {
        return new static(ReflectionFactory::getMethod($objectOrClass, $method));
    }

    /**
     * @return ReflectionParameter[]
     */
    public function get(): array
    {
        return $this->method->getParameters();
    }
}