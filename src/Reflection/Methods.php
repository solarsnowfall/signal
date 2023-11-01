<?php

namespace Signal\Reflection;

use ReflectionMethod;

class Methods extends ClassMembers
{
    protected ?bool $abstract = null;

    public function abstract(?bool $abstract): static
    {
        $this->abstract = $abstract;
        return $this;
    }

    protected function isAbstract(ReflectionMethod $method): bool
    {
        return is_null($this->abstract) || $this->abstract === $method->isAbstract();
    }

    public function get(): array
    {
        return array_filter(
            array: $this->class->getMethods($this->visibility),
            callback: fn(ReflectionMethod $method) => $this->filterMethod($method)
        );
    }

    protected function filterMethod(ReflectionMethod $method): bool
    {
        return $this->filter($method) && $this->isAbstract($method);
    }
}