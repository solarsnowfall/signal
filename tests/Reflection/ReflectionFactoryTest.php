<?php

namespace Signal\Tests\Reflection;

use PHPUnit\Framework\TestCase;
use ReflectionClass;
use Signal\Reflection\ReflectionFactory;
use Signal\Tests\Util\TestDependency;
use Signal\Tests\Util\TestService;

class ReflectionFactoryTest extends TestCase
{
    public static function objectProvider(): array
    {
        return [
            [TestService::class, new TestService(new TestDependency)]
        ];
    }

    public function testClassNameWithClass()
    {
        $class = ReflectionFactory::className(TestService::class);
        $this->assertEquals(TestService::class, $class);
    }

    /** @dataProvider objectProvider */
    public function testClassNameWithObject(string $expected, object $object)
    {
        $class = ReflectionFactory::className($object);
        $this->assertEquals($expected, $class);
    }

    public function testClassShortNameWithClass()
    {
        $class = ReflectionFactory::classShortName(TestService::class);
        $parts = explode('\\', TestService::class);
        $this->assertEquals(array_pop($parts), $class);
    }

    /** @dataProvider objectProvider */
    public function testClassShortNameWithObject(string $expected, object $object)
    {
        $class = ReflectionFactory::classShortName($object);
        $parts = explode('\\', $expected);
        $this->assertEquals(array_pop($parts), $class);
    }

    public function testGetClassWithClass()
    {
        $class = ReflectionFactory::getClass(TestService::class);
        $this->assertInstanceOf(ReflectionClass::class, $class);
        $this->assertEquals(TestService::class, $class->getName());
    }

    /** @dataProvider objectProvider */
    public function testGetClassWithObject(string $expected, object $object)
    {
        $class = ReflectionFactory::getClass($object);
        $this->assertInstanceOf(ReflectionClass::class, $class);
        $this->assertEquals($expected, $class->getName());
    }
}