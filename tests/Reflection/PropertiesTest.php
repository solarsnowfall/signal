<?php

namespace Signal\Tests\Reflection;


use Signal\Reflection\Properties;
use Signal\Tests\Util\TestService;

class PropertiesTest extends ClassMembersTestAbstract
{
    public static function getExpectedCount(string $method): int
    {
        return match($method) {
            'testGet' => 11,
            'testGetPublics' => 5,
            'testGetProtecteds' => 4,
            'testGetPrivates' => 2,
            'testGetStatics' => 5,
            'testGetNonStatics' => 6,
            'testGetNatives' => 7,
            'testGetNonNatives' => 4,
            'testGetDefaults' => 11,
            'testGetNonDefaults' => 0
        };
    }

    public static function instanceProvider(): array
    {
        return [
            [new Properties(new \ReflectionClass(TestService::class))],
            [Properties::withClass(TestService::class)]
        ];
    }

    /** @dataProvider instanceProvider */
    public function testGetDefaults(Properties $instance)
    {
        $properties = $instance->default(true)->get();
        $this->assertCount(static::getExpectedCount(__FUNCTION__), $properties);

        foreach ($properties as $property) {
            $this->assertTrue($property->isDefault());
        }
    }

    /** @dataProvider instanceProvider */
    public function testGetNonDefaults(Properties $instance)
    {
        $properties = $instance->default(false)->get();
        $this->assertCount(static::getExpectedCount(__FUNCTION__), $properties);

        foreach ($properties as $property) {
            $this->assertFalse($property->isDefault());
        }
    }
}