<?php declare(strict_types=1);

namespace PHPUGDD\PHPDD\Website\Bin;

use PHPUGDD\PHPDD\Website\Tickets\Application\Cli\ConsoleCommands\SendOrderMailsCommand;
use PHPUGDD\PHPDD\Website\Tickets\Env;
use Symfony\Component\Console\Application;

require __DIR__ . '/../vendor/autoload.php';

$env = new Env();

/** @noinspection PhpUnhandledExceptionInspection */
$env->getErrorHandler()->install();

$app = new Application( 'PHPDD18 CLI', '1.0' );
$app->add( new SendOrderMailsCommand( 'send:ordermails', $env ) );

$exitCode = $app->run();

exit( $exitCode );
