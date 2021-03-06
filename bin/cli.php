<?php declare(strict_types=1);

namespace PHPUGDD\PHPDD\Website\Bin;

use PHPUGDD\PHPDD\Website\Tickets\Application\Cli\ConsoleCommands\ChangeAttendeeNameCommand;
use PHPUGDD\PHPDD\Website\Tickets\Application\Cli\ConsoleCommands\ExportAttendeesCommand;
use PHPUGDD\PHPDD\Website\Tickets\Application\Cli\ConsoleCommands\ExportConferenceAttendeesCommand;
use PHPUGDD\PHPDD\Website\Tickets\Application\Cli\ConsoleCommands\ExportWorkshopAttendeesCommand;
use PHPUGDD\PHPDD\Website\Tickets\Application\Cli\ConsoleCommands\RefundTicketsCommand;
use PHPUGDD\PHPDD\Website\Tickets\Application\Cli\ConsoleCommands\SendOrderMailsCommand;
use PHPUGDD\PHPDD\Website\Tickets\Application\Cli\ConsoleCommands\SendTicketSaleSummary;
use PHPUGDD\PHPDD\Website\Tickets\Env;
use Symfony\Component\Console\Application;
use Throwable;

require __DIR__ . '/../vendor/autoload.php';

$env = new Env();

/** @noinspection PhpUnhandledExceptionInspection */
$env->getErrorHandler()->install();

$app = new Application( 'PHPDD18 CLI', '1.0' );
try
{
	$app->add( new SendOrderMailsCommand( 'send:ordermails', $env ) );
	$app->add( new SendTicketSaleSummary( 'send:salesummary', $env ) );
	$app->add( new RefundTicketsCommand( 'refund:tickets', $env ) );
	$app->add( new ChangeAttendeeNameCommand( 'change:attendee', $env ) );
	$app->add( new ExportAttendeesCommand( 'export:attendees', $env ) );
	$app->add( new ExportWorkshopAttendeesCommand( 'export:workshop-attendees', $env ) );
	$app->add( new ExportConferenceAttendeesCommand( 'export:conference-attendees', $env ) );

	$exitCode = $app->run();
}
catch ( Throwable $e )
{
	$env->getErrorHandler()->captureException( $e );

	$exitCode = 1;
}

exit( $exitCode );
