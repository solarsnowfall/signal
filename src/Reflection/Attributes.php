<?php

namespace Signal\Reflection;

use ReflectionAttribute;
use ReflectionClass;
use ReflectionMethod;
use ReflectionProperty;
use Signal\Traits\WithReflectionClass;
use Signal\Traits\WithReflectionMethod;
use Signal\Traits\WithReflectionProperty;

class Attributes
{
    use WithReflectionClass;
    use WithReflectionMethod;
    use WithReflectionProperty;
    private int $flags = 0;

    private ?string $name = null;

    public function __construct(
        private readonly ReflectionClass|ReflectionMethod|ReflectionProperty $reflection
    ) {}

    public static function withReflection(ReflectionClass|ReflectionMethod|ReflectionProperty $reflection): static
    {
        return new static($reflection);
    }

    public static function forClass(object|string $objectOrClass): static
    {
        return new static(
            $objectOrClass instanceof ReflectionClass
                ? $objectOrClass
                : ReflectionFactory::getClass($objectOrClass
            )
        );
    }

    public static function forMethod(object|string $objectOrClass, ?string $method = null): static
    {
        return new static(
            $objectOrClass instanceof ReflectionMethod
            ? $objectOrClass
            : ReflectionFactory::getMethod($objectOrClass, $method)
        );
    }

    public static function forProperty(object|string $objectOrClass, ?string $property = null): static
    {
        return new static(
            $objectOrClass instanceof ReflectionProperty
            ? $objectOrClass
            : ReflectionFactory::getProperty($objectOrClass, $property)
        );
    }

    public function flags($flags): static
    {
        $this->flags = $flags;
        return $this;
    }

    public function name(?string $name): static
    {
        $this->name = $name;
        return $this;
    }

    public function instanceOf(string $name): static
    {
        return $this->name($name)->flags(ReflectionAttribute::IS_INSTANCEOF);
    }

    public function get(): array
    {
        return $this->reflection->getAttributes($this->name, $this->flags);
    }

    public function first(): ?ReflectionAttribute
    {
        return $this->reflection->getAttributes($this->name, $this->flags)[0] ?? null;
    }

    public function instances(): array
    {
        return array_map(fn(ReflectionAttribute $attribute) => $attribute->newInstance(), $this->get());
    }

    public function getInstances(): array
    {
        return array_map(fn(ReflectionAttribute $attribute) => $attribute->newInstance(), $this->get());
    }

    public function has(ReflectionAttribute|string $attribute): bool
    {
        $name = $attribute instanceof ReflectionAttribute ? $attribute->getName() : $attribute;
        return count($this->reflection->getAttributes($name)) > 0;
    }
}