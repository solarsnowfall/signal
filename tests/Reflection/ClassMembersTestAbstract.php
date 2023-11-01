<?php

namespace Signal\Tests\Reflection;

use PHPUnit\Framework\TestCase;
use Signal\Reflection\ClassMembers;
use Signal\Reflection\Methods;
use Signal\Reflection\Visibility;
use Signal\Tests\Util\AbstractTestService;
use Signal\Tests\Util\TestService;

abstract class ClassMembersTestAbstract extends TestCase
{
    abstract public static function instanceProvider(): array;
    
    abstract public static function getExpectedCount(string $method): int;

    /** @dataProvider instanceProvider */
    public function testGet(ClassMembers $instance)
    {
        $members = $instance->get();
        $this->assertCount(static::getExpectedCount(__FUNCTION__), $members);
    }

    /** @dataProvider instanceProvider */
    public function testGetPublics(ClassMembers $instance)
    {
        $members = $instance->visibility(Visibility::PUBLIC)->get();
        $this->assertCount(static::getExpectedCount(__FUNCTION__), $members);
    }

    /** @dataProvider instanceProvider */
    public function testGetProtecteds(ClassMembers $instance)
    {
        $members = $instance->visibility(Visibility::PROTECTED)->get();
        $this->assertCount(static::getExpectedCount(__FUNCTION__), $members);
    }

    /** @dataProvider instanceProvider */
    public function testGetPrivates(ClassMembers $instance)
    {
        $members = $instance->visibility(Visibility::PRIVATE)->get();
        $this->assertCount(static::getExpectedCount(__FUNCTION__), $members);
    }

    /** @dataProvider instanceProvider */
    public function testGetStatics(ClassMembers $instance)
    {
        $members = $instance->static(true)->get();
        $this->assertCount(static::getExpectedCount(__FUNCTION__), $members);

        foreach ($members as $member) {
            $this->assertTrue($member->isStatic());
        }
    }

    /** @dataProvider instanceProvider */
    public function testGetNonStatics(ClassMembers $instance)
    {
        $members = $instance->static(false)->get();
        $this->assertCount(static::getExpectedCount(__FUNCTION__), $members);

        foreach ($members as $member) {
            $this->assertFalse($member->isStatic());
        }
    }

    /** @dataProvider instanceProvider */
    public function testGetNatives(ClassMembers $instance)
    {
        $members = $instance->native(true)->get();
        $this->assertCount(static::getExpectedCount(__FUNCTION__), $members);

        foreach ($members as $member) {
            $this->assertTrue(
                TestService::class === $member->getDeclaringClass()->getName()
            );
        }
    }

    /** @dataProvider instanceProvider */
    public function testGetNonNatives(ClassMembers $instance)
    {
        $members = $instance->native(false)->get();
        $this->assertCount(static::getExpectedCount(__FUNCTION__), $members);

        foreach ($members as $member) {
            $this->assertTrue(
                TestService::class !== $member->getDeclaringClass()->getName()
            );
        }
    }
}