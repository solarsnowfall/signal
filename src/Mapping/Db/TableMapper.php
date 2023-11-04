<?php

namespace Signal\Mapping\Db;

use Signal\Mapping\Mapper;

class TableMapper extends Mapper
{

    public static function propertyAttributes(): array
    {
        return [
            Column::class
        ];
    }
}