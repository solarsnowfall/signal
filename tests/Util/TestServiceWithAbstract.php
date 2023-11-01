<?php

namespace Signal\Tests\Util;

class TestServiceWithAbstract
{
    public function __construct(private AbstractTestDependency $dependency)
    {}
}