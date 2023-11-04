<?php

namespace Signal\Mapping;

use PHPUnit\Framework\Attributes\Ticket;
use ReflectionProperty;
use Signal\Reflection\Attributes;
use Signal\Reflection\Properties;
use Signal\Reflection\ReflectionFactory;
use Signal\Support\Collection;

abstract class Mapper
{
    protected array $propertyAttributes = [];

    /**
     * @param object|string $objectOrClass
     * @return ReflectionProperty[]
     */
    public function getMappableProperties(object|string $objectOrClass): array
    {
        $properties = [];

        foreach (Properties::withClass($objectOrClass)->get() as $property) {
            foreach ($this->propertyAttributes as $attribute) {
                if (Attributes::withProperty($property)->has($attribute)) {
                    $properties[$property->getName()] = $property;
                }
            }
        }

        return $properties;
    }

    public function mapProperties(object $target, object|array $source): void
    {
        $properties = is_object($source) ? self::extractProperties($source) : $source;

        foreach (static::getMappableProperties($target) as $property) {
            if (isset($properties[$property->getName()])) {
                $property->setValue($target, $properties[$property->getName()]);
            }
        }
    }

    public function extractProperties(object $source): array
    {
        $values = [];
        foreach (ReflectionFactory::getClass($source)->getProperties() as $property) {
            $values[$property->getName()] = $property->getValue($source);
        }

        return $values;
    }
}