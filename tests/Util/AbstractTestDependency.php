<?php

namespace Signal\Tests\Util;

class AbstractTestDependency
{
    public function __construct(
        public ?int $a = null,
        public ?int $b = null,
        public ?int $c = null
    ) {}
}