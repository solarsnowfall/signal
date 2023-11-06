<?php

include 'vendor/autoload.php';

$cache = new \Signal\Cache\Adapters\FileCache();
$cache->set('test', 'toast', 100);
sleep(1);
var_dump($cache->get('test'));
