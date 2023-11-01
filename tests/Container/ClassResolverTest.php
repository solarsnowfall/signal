<?php

namespace Signal\Tests\Container;

use PHPUnit\Framework\TestCase;
use ReflectionClass;
use Signal\Dependency\ClassResolver;
use Signal\Dependency\Container;
use Signal\Tests\Util\TestDependency;
use Signal\Tests\Util\TestService;

class ClassResolverTest extends TestCase
{
    protected Container $container;

    protected function setUp(): void
    {
        $this->container = Container::getContainer();
    }

    public function testResolveClass()
    {
        $this->container->set(TestService::class);
        $this->container->set(TestDependency::class);
        $resolver = new ClassResolver(new ReflectionClass(TestService::class));
        $this->assertInstanceOf(TestService::class, $resolver->createInstance());
    }
}