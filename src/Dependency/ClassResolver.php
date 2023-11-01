<?php

namespace Signal\Dependency;

use Psr\Container\ContainerExceptionInterface;
use ReflectionClass;
use ReflectionException;
use ReflectionMethod;
use ReflectionParameter;
use RuntimeException;
use Signal\Reflection\ReflectionFactory;
use Signal\Support\Arr;

class ClassResolver
{
    public function __construct(
        private readonly ReflectionClass $class
    ){}

    public static function for(object|string $objectOrClass): static
    {
        return new static(ReflectionFactory::getClass($objectOrClass));
    }

    public function createInstance(array $arguments = []): object
    {
        try {
            return $this->class->newInstance(...$this->getDependencies($arguments));
        } catch (ReflectionException $exception) {
            throw new RuntimeException(
                message: sprintf("Failed to create instance of %s", $this->class->getName()),
                previous: $exception
            );
        }
    }

    private function getDependencies(array $arguments): array
    {
        $constructor = $this->class->getConstructor();

        if (is_null($constructor)) {
            return [];
        }

        return $this->resolveDependencies($constructor, $arguments);
    }

    private function resolveDependencies(ReflectionMethod $constructor, array $arguments): array
    {
        $sequential = Arr::isSequential($arguments);
        $dependencies = [];

        foreach ($constructor->getParameters() as $key => $parameter) {
            if ($sequential && isset($arguments[$key])) {
                $dependencies[] = $arguments[$key];
            } elseif (!$sequential && isset($arguments[$parameter->getName()])) {
                $dependencies[] = $arguments[$parameter->getName()];
            } elseif (null !== $instance = $this->resolveParameter($parameter)) {
                $dependencies[] = $instance;
            } elseif ($parameter->isDefaultValueAvailable()) {
                $dependencies[] = $parameter->getDefaultValue();
            }
        }

        return $dependencies;
    }

    /**
     * @param ReflectionParameter $parameter
     * @return mixed
     * @throws ContainerExceptionInterface
     */
    private function resolveParameter(ReflectionParameter $parameter): mixed
    {
        return ParameterResolver::for($parameter)->resolveInstance();
    }
}