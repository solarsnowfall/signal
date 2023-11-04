<?php

namespace Signal\Reflection;

use ReflectionAttribute;
use ReflectionProperty;
use Signal\Reflection\Attributes\Attributes;
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

    /**
     * @return Collection|ReflectionProperty[]
     */
    public function get(): Collection|array
    {
        return Collection::for($this->class->getProperties($this->visibility))
            ->filter(fn(ReflectionProperty $property) => $this->filterProperty($property));
    }

    protected function filterProperty(ReflectionProperty $property): bool
    {
        return $this->filter($property) && $this->isDefault($property);
    }
}