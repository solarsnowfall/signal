<?php

namespace Signal\Tests\Util;

#[TestAttribute(type: 'class')]
class TestService extends AbstractTestService
{
    #[TestAttribute(type: 'property')]
    public $publicProperty;
    public static $publicStaticProperty;
    protected $protectedProperty;
    protected static $protectedStaticProperty;
    private $privateProperty;
    private static $privateStaticProperty;

    #[TestAttribute(type: 'method')]
    public function __construct(
        public TestDependency $dependency
    ) {}

    protected function protectedMethod()
    {
    }

    protected static function protectedStaticMethod()
    {
    }

    private function privateMethod()
    {
    }

    private static function privateStaticMethod()
    {
    }

    public function abstractPublicMethod()
    {
    }

    protected function abstractProtectedMethod()
    {
    }

    public static function abstractPublicStaticMethod()
    {
        // TODO: Implement abstractPublicStaticMethod() method.
    }

    protected static function abstractProtectedStaticMethod()
    {
        // TODO: Implement abstractProtectedStaticMethod() method.
    }
}