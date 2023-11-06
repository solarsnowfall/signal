<?php

namespace Signal\Tests\Util;

use Serializable;
use Signal\Tests\Util\Traits\SerializesObject;

abstract class AbstractTestService
{
    use SerializesObject;

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