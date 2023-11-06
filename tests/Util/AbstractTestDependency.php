<?php

namespace Signal\Tests\Util;

use Signal\Tests\Util\Traits\SerializesObject;

class AbstractTestDependency
{
    use SerializesObject;

    public function __construct(
        public ?int $a = null,
        public ?int $b = null,
        public ?int $c = null
    ) {}
}