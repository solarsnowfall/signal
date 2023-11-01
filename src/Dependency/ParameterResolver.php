<?php

namespace Signal\Dependency;

use Psr\Container\ContainerExceptionInterface;
use ReflectionNamedType;
use ReflectionParameter;

class ParameterResolver
{
    /**
     * @param ContainerInterface $container
     * @param ReflectionParameter $parameter
     */
    public function __construct(
        private readonly ContainerInterface $container,
        private readonly ReflectionParameter $parameter
    ) {}

    /**
     * @param ReflectionParameter $parameter
     * @return static
     */
    public static function for(ReflectionParameter $parameter): static
    {
        return new static(container: Container::getContainer(), parameter: $parameter);
    }

    /**
     * @return mixed
     * @throws ContainerExceptionInterface
     */
    public function resolveInstance(): mixed
    {
        $types = $this->getTypes();
        return $this->findClass($types)
            ?? $this->findInterface($types)
            ?? $this->findChildClass($types);
    }

    /**
     * @return ReflectionNamedType[]
     */
    private function getTypes(): array
    {
        $type = $this->parameter->getType();
        return !is_a($type, ReflectionNamedType::class)
            ? $type->getTypes()
            : [$type];
    }

    /**
     * @param ReflectionNamedType[] $types
     * @return mixed
     * @throws ContainerExceptionInterface
     */
    private function findClass(array $types): mixed
    {
        foreach ($types as $type) {
            if (class_exists($type->getName()) && $this->container->has($type->getName())) {
                return $this->container->get($type->getName());
            }
        }

        return null;
    }

    /**
     * @param ReflectionNamedType[] $types
     * @return mixed
     * @throws ContainerExceptionInterface
     */
    private function findInterface(array $types): mixed
    {
        foreach ($types as $type) {
            if (!interface_exists($type->getName())) {
                continue;
            }

            foreach (get_declared_classes() as $class) {
                if (
                    $this->container->has($class)
                    && in_array($type->getName(), class_implements($class))
                ) {
                    return $this->container->get($class);
                }
            }
        }

        return null;
    }

    /**
     * @param ReflectionNamedType[] $types
     * @return mixed
     * @throws ContainerExceptionInterface
     */
    public function findChildClass(array $types): mixed
    {
        foreach ($types as $type) {
            if (!class_exists($type->getName())) {
                continue;
            }

            foreach (get_declared_classes() as $class) {
                if ($this->container->has($class) && is_subclass_of($class, $type->getName())) {
                    return $this->container->get($class);
                }
            }
        }

        return null;
    }
}