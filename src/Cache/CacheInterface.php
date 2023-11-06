<?php

namespace Signal\Cache;

use Psr\Cache\CacheItemPoolInterface;
use Psr\SimpleCache\CacheInterface as PsrCacheInterface;

interface CacheInterface extends CacheItemPoolInterface, PsrCacheInterface
{}