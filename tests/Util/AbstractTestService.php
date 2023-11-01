<?php

namespace Signal\Tests\Util;

abstract class AbstractTestService
{
    public $publicNonNativeProperty;
    public static $publicStaticNonNativeProperty;
    protected $protectedNonNativeProperty;
    protected static $protectedStaticNonNativeProperty;

    abstract public function abstractPublicMethod();

    abstract public static function abstractPublicStaticMethod();

    abstract protected function abstractProtectedMethod();

    abstract protected static function abstractProtectedStaticMethod();

    public function nonNativePublicMethod()
    {
    }

    public static function nonNativePublicStaticMethod()
    {
    }

    public function nonNativeProtectedMethod()
    {
    }

    protected static function nonNativeProtectedStaticMethod()
    {
    }
}