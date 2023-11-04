<?php

namespace Signal\Mapping\Db;

use ReflectionProperty;
use Signal\Mapping\Mapper;
use Signal\Reflection\Attributes;
use Signal\Reflection\Properties;
use Signal\Support\Collection;

class TableRowMapper extends Mapper
{
    protected array $propertyAttributes = [
        Column::class
    ];

    public function __construct(
        private readonly string $table
    ) {}

    public static function withTable(string $table): static
    {
        return new static($table);
    }
}