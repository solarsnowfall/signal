<?php

include 'vendor/autoload.php';

$disk = new \Signal\Filesystem\Adapters\DiskFilesystem('tests/Cache/cache');
foreach ($disk->listDirectory('') as $file) {
    echo "{$file->getPathname()}\n";
}
