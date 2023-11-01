<?php

namespace Signal\Reflection;

use ReflectionProperty;

class Visibility
{
    const ALL = null;
    const PUBLIC = ReflectionProperty::IS_PUBLIC;
    const PROTECTED = ReflectionProperty::IS_PROTECTED;
    const PRIVATE = ReflectionProperty::IS_PRIVATE;
    const PARENT_ACCESSIBLE = self::PUBLIC | self::PROTECTED;
}