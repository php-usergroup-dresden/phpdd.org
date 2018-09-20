<?php declare(strict_types=1);

namespace PHPUGDD\PHPDD\Website\Tickets\Application\Cli\ConsoleCommands;

use PHPUGDD\PHPDD\Website\Tickets\Application\Tickets\Repositories\TicketOrderRepository;
use SplFileObject;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Throwable;
use function get_class;

final class ExportAttendeesCommand extends AbstractConsoleCommand
{
	protected function configure() : void
	{
		$this->addArgument( 'outfile', InputArgument::OPTIONAL, 'Path to outfile', 'attendees.csv' );
		$this->setDescription( 'Export all attendees to CSV grouped by email address.' );
	}

	/**
	 * @param InputInterface  $input
	 * @param OutputInterface $output
	 *
	 * @throws \Symfony\Component\Console\Exception\InvalidArgumentException
	 * @return int
	 */
	protected function execute( InputInterface $input, OutputInterface $output ) : int
	{
		$this->initStyle( $input, $output );
		$style      = $this->getStyle();
		$outfile    = $input->getArgument( 'outfile' );
		$database   = $this->getEnv()->getDatabase();
		$repository = new TicketOrderRepository( $database );

		$style->title( 'Export of all attendees' );

		try
		{
			$csv = new SplFileObject( $outfile, 'wb' );
			$csv->setCsvControl( ',', '"', '\\' );

			$csv->fputcsv( ['email', 'firstname', 'lastname', 'attendees'] );

			foreach ( $repository->getAllAttendeesGroupedByEmail() as $record )
			{
				$csv->fputcsv( $record, ',', '"', '\\' );
			}

			$style->success( 'CSV file saved to ' . $outfile );

			return 0;
		}
		catch ( Throwable $e )
		{
			$style->error( get_class( $e ) . ': ' . $e->getMessage() . "\n" . $e->getTraceAsString() );

			return 1;
		}
	}
}