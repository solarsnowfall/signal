<?php

namespace Signal\Dependency;

use Psr\Container\ContainerInterface as PsrContainerInterface;

interface ContainerInterface extends PsrContainerInterface
{
    public function forget(string $id): void;

    public static function getContainer(): ContainerInterface;

    public function make(string $class, array $arguments = null): object;

    public function set(string $class, object|array|null $definition = null, bool $singleton = false): void;

    public function singleton(string $id, object|array|null $definition = null): void;
}