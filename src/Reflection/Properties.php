<?php

namespace Signal\Reflection;

use ReflectionProperty;
use Signal\Support\Collection;

class Properties extends ClassMembers
{
    protected ?bool $default = null;

    public function default(?bool $default): static
    {
        $this->default = $default;
        return $this;
    }

    protected function isDefault(ReflectionProperty $property): bool
    {
        return is_null($this->default) || $this->default === $property->isDefault();
    }

    public function get(): Collection
    {
        return Collection::for($this->class->getProperties($this->visibility))
            ->filter(fn(ReflectionProperty $property) => $this->filterProperty($property));
    }

    public function attributes(): Collection
    {
        return $this->get()
            ->filter(fn(ReflectionProperty $property) => count($property->getAttributes()))
            ->map(fn(ReflectionProperty $property) => Attributes::for($property)->first());
    }

    protected function filterProperty(ReflectionProperty $property): bool
    {
        return $this->filter($property) && $this->isDefault($property);
    }
}