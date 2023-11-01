<?php

include 'vendor/autoload.php';

var_dump(
    \Signal\Reflection\Properties::for(\Signal\Tests\Util\TestService::class)->attributes()
);