<?php declare(strict_types=1);

/** @noinspection PhpUndefinedConstantInspection */
/** @noinspection PhpUndefinedFunctionInspection */
xdebug_set_filter( XDEBUG_FILTER_CODE_COVERAGE, XDEBUG_PATH_WHITELIST, [__DIR__ . '/../src/Tickets'] );

require __DIR__ . '/../vendor/autoload.php';
