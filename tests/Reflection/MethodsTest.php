<?php

namespace Signal\Tests\Reflection;

use PhpParser\Builder\Method;
use Signal\Reflection\ClassMembers;
use Signal\Reflection\Methods;
use Signal\Tests\Util\AbstractTestService;
use Signal\Tests\Util\TestDependency;
use Signal\Tests\Util\TestService;

class MethodsTest extends ClassMembersTestAbstract
{
    public static function instanceProvider(): array
    {
        return [
            [new Methods(new \ReflectionClass(TestService::class))],
            [Methods::for(TestService::class)]
        ];
    }

    public static function getExpectedCount(string $method): int
    {
        return match($method) {
            'testGet' => 13,
            'testGetPublics' => 6,
            'testGetProtecteds' => 5,
            'testGetPrivates' => 2,
            'testGetStatics' => 6,
            'testGetNonStatics' => 7,
            'testGetNatives' => 9,
            'testGetNonNatives' => 4,
            'testGetAbstracts' => 4,
            'testGetNonAbstracts' => 4
        };
    }

    /** @dataProvider instanceProvider */
    public function testGetAbstracts(Methods $instance)
    {
        $methods = Methods::for(AbstractTestService::class)->abstract(true)->get();
        $this->assertCount(static::getExpectedCount(__FUNCTION__), $methods);

        foreach ($methods as $method) {
            $this->assertTrue($method->isAbstract());
        }
    }

    /** @dataProvider instanceProvider */
    public function testGetNonAbstracts(Methods $instance)
    {
        $methods = Methods::for(AbstractTestService::class)->abstract(false)->get();
        $this->assertCount(static::getExpectedCount(__FUNCTION__), $methods);

        foreach ($methods as $method) {
            $this->assertFalse($method->isAbstract());
        }
    }
}