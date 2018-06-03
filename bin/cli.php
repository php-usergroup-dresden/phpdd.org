<?php declare(strict_types=1);

namespace PHPUGDD\PHPDD\Website\Bin;

use PHPUGDD\PHPDD\Website\Tickets\Application\Cli\ConsoleCommands\SendOrderMailsCommand;
use PHPUGDD\PHPDD\Website\Tickets\Application\Cli\ConsoleCommands\SendTicketSaleSummary;
use PHPUGDD\PHPDD\Website\Tickets\Env;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Exception\LogicException;

require __DIR__ . '/../vendor/autoload.php';

$env = new Env();

/** @noinspection PhpUnhandledExceptionInspection */
$env->getErrorHandler()->install();

$app = new Application( 'PHPDD18 CLI', '1.0' );
try
{
	$app->add( new SendOrderMailsCommand( 'send:ordermails', $env ) );
	$app->add( new SendTicketSaleSummary( 'send:salesummary', $env ) );

	$exitCode = $app->run();
}
catch ( LogicException $e )
{
	$env->getErrorHandler()->captureException( $e );

	$exitCode = 1;
}

exit( $exitCode );
