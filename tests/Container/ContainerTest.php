<?php

namespace Signal\Tests\Container;

use Exception;
use PHPUnit\Framework\TestCase;
use Signal\Dependency\Container;
use Signal\Dependency\NotFoundException;
use Signal\Tests\Util\TestDependency;
use Signal\Tests\Util\TestService;
use Throwable;

class ContainerTest extends TestCase
{
    protected Container $container;

    protected function setUp(): void
    {
        $this->container = new Container();
    }

    public function testHas()
    {
        $this->container->set(TestDependency::class);
        $this->assertTrue($this->container->has(TestDependency::class));
    }

    public function testHasNot()
    {
        $this->assertFalse($this->container->has(TestDependency::class));
    }

    public function testGetThrowsExceptionWhenHasNot()
    {
        try {
            $this->container->get(TestDependency::class);
        } catch (Throwable $exception) {
            $this->assertInstanceOf(NotFoundException::class, $exception);
        }
    }

    public function testGetClassWithNullDefinition()
    {
        $this->container->set(TestDependency::class, null);
        $this->assertInstanceOf(TestDependency::class, $this->container->get(TestDependency::class));
    }

    public function testGetClassWithObjectDefinition()
    {
        $this->container->set(TestDependency::class, new TestDependency(1, 2, 3));
        $instance = $this->container->get(TestDependency::class);
        $this->assertInstanceOf(TestDependency::class, $instance);
        $this->assertEquals(1, $instance->a);
        $this->assertEquals(2, $instance->b);
        $this->assertEquals(3, $instance->c);
    }

    public function testGetClassWithSequentialArrayDefinition()
    {
        $definition = [1, 2, 3];
        $this->container->set(TestDependency::class, $definition);
        $instance = $this->container->get(TestDependency::class);
        $this->assertInstanceOf(TestDependency::class, $instance);
        $this->assertEquals($definition[0], $instance->a);
        $this->assertEquals($definition[1], $instance->b);
        $this->assertEquals($definition[2], $instance->c);
    }

    public function testGetClassWithAssociativeArrayDefinition()
    {
        $definition = ['a' => 1, 'b' => 2, 'c' => 3];
        $this->container->set(TestDependency::class, $definition);
        $instance = $this->container->get(TestDependency::class);
        $this->assertInstanceOf(TestDependency::class, $instance);
        $this->assertEquals($definition['a'], $instance->a);
        $this->assertEquals($definition['b'], $instance->b);
        $this->assertEquals($definition['c'], $instance->c);
    }

    public function testGetClassWithNestedDependencies()
    {
        $this->container->set(TestDependency::class, [1, 2, 3]);
        $this->container->set(TestService::class);
        $instance = $this->container->get(TestService::class);
        $this->assertInstanceOf(TestDependency::class, $instance->dependency);
    }
}