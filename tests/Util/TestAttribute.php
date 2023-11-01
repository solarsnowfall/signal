<?php

namespace Signal\Tests\Util;

use Attribute;

#[Attribute]
class TestAttribute
{
    public function __construct(
        public readonly ?string $type = null
    ) {}
}