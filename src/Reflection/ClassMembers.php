<?php

namespace Signal\Reflection;

use ReflectionClass;
use ReflectionMethod;
use ReflectionProperty;

abstract class ClassMembers
{
    protected ?bool $native = null;

    protected ?bool $static = null;

    protected ?int $visibility = Visibility::ALL;

    public function __construct(
        protected readonly ReflectionClass $class
    ) {}

    public static function for(string $objectOrClass): static
    {
        return new static(ReflectionFactory::getClass($objectOrClass));
    }

    public function native(?bool $native): static
    {
        $this->native = $native;
        return $this;
    }

    public function static(?bool $static): static
    {
        $this->static = $static;
        return $this;
    }

    public function visibility(?int $visibility): static
    {
        $this->visibility = $visibility;
        return $this;
    }

    protected function filter(ReflectionMethod|ReflectionProperty $member): bool
    {
        return $this->isStatic($member) && $this->isNative($member);
    }

    protected function isNative(ReflectionMethod|ReflectionProperty $member): bool
    {
        return is_null($this->native) || $this->native === (
            $this->class->getName() === $member->getDeclaringClass()->getName()
        );
    }

    protected function isStatic(ReflectionMethod|ReflectionProperty $member): bool
    {
        return is_null($this->static) || $this->static === $member->isStatic();
    }

    protected function memberVisibility(ReflectionMethod|ReflectionProperty $member): int
    {
        return $member->isPublic() ? Visibility::PUBLIC : (
            $member->isProtected() ? Visibility::PROTECTED : Visibility::PRIVATE
        );
    }
}