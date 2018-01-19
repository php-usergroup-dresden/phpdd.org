<?php declare(strict_types=1);

xdebug_set_filter( XDEBUG_FILTER_CODE_COVERAGE, XDEBUG_PATH_WHITELIST, [__DIR__ . '/../src'] );

require __DIR__ . '/../vendor/autoload.php';
