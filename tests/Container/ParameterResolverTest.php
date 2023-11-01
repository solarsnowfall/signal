<?php

namespace Signal\Tests\Container;

use PHPUnit\Framework\TestCase;
use ReflectionParameter;
use Signal\Dependency\Container;
use Signal\Dependency\ParameterResolver;
use Signal\Tests\Util\TestDependency;
use Signal\Tests\Util\TestService;
use Signal\Tests\Util\TestServiceWithAbstract;
use Signal\Tests\Util\TestServiceWithInterface;

class ParameterResolverTest extends TestCase
{
    protected Container $container;

    protected function setUp(): void
    {
        $this->container = new Container();
    }

    public function testResolveInstanceFromClass()
    {
        $this->container->set(TestService::class);
        $this->container->set(TestDependency::class);

        $resolver = new ParameterResolver($this->container, new ReflectionParameter(
            [TestService::class, '__construct'], 'dependency'
        ));

        $this->assertInstanceOf(TestDependency::class, $resolver->resolveInstance());
    }

    public function testResolveInstanceFromInterface()
    {
        $this->container->set(TestServiceWithInterface::class);
        $this->container->set(TestDependency::class);

        $resolver = new ParameterResolver($this->container, new ReflectionParameter(
            [TestService::class, '__construct'], 'dependency'
        ));

        $this->assertInstanceOf(TestDependency::class, $resolver->resolveInstance());
    }

    public function testResolveChildClass()
    {
        $this->container->set(TestServiceWithAbstract::class);
        $this->container->set(TestDependency::class);

        $resolver = new ParameterResolver($this->container, new ReflectionParameter(
            [TestService::class, '__construct'], 'dependency'
        ));

        $this->assertInstanceOf(TestDependency::class, $resolver->resolveInstance());
    }
}