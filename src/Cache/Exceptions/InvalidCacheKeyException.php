<?php

namespace Signal\Cache\Exceptions;

use Psr\Cache\InvalidArgumentException;
use Psr\SimpleCache\InvalidArgumentException as SimpleInvalidArgumentException;
use RuntimeException;

class InvalidCacheKeyException
    extends RuntimeException
    implements InvalidArgumentException, SimpleInvalidArgumentException
{}