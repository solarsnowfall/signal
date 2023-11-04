<?php

namespace Signal\Mapping\Db;

use Attribute;

#[Attribute]
class Column
{
    public function __construct(
        public readonly string $name,
        public readonly ?string $table = null,
        public readonly ?string $type = null,
        public readonly ?string $default = null,
        public readonly ?bool $nullable = null,
        public readonly ?int $maxlength = null,
        public readonly ?int $precision = null,
        public readonly ?int $scale = null,
        public readonly ?string $key = null,
    ) {}
}