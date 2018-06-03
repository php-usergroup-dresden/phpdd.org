<?php declare(strict_types=1);

namespace PHPUGDD\PHPDD\Website\Tickets\Application\Cli\ConsoleCommands;

use DateTimeImmutable;
use PHPUGDD\PHPDD\Website\Tickets\Application\Tickets\Repositories\TicketOrderRepository;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Throwable;
use function strtotime;

final class SendTicketSaleSummary extends AbstractConsoleCommand
{
	protected function configure() : void
	{
		$this->setDescription( 'Sends a ticket sale summary to a slack channel.' );
		$this->addOption(
			'channel',
			'c',
			InputOption::VALUE_OPTIONAL,
			'Slack channel to send the summary to'
		);
		$this->addOption(
			'date',
			'd',
			InputOption::VALUE_OPTIONAL,
			'Date for the day of the summary',
			date( 'Y-m-d', strtotime( 'yesterday' ) )
		);
	}

	/**
	 * @param InputInterface  $input
	 * @param OutputInterface $output
	 *
	 * @throws \Exception
	 * @return int
	 */
	protected function execute( InputInterface $input, OutputInterface $output ) : int
	{
		$this->initStyle( $input, $output );
		$style       = $this->getStyle();
		$database    = $this->getEnv()->getDatabase();
		$slackClient = $this->getEnv()->getSlackClient();
		$date        = new DateTimeImmutable( $input->getOption( 'date' ) );
		$channel     = $input->getOption( 'channel' );

		$style->section( sprintf( 'Generating summary for %s', $date->format( 'Y-m-d' ) ) );

		$ticketOrderRepository = new TicketOrderRepository( $database );

		try
		{
			$summary = $ticketOrderRepository->getTicketSaleSummary( $date );

			$slackClient->sendSummary( $summary, $channel );

			$style->success( 'Summary sent to ' . ($channel ?: 'default channel') );
		}
		catch ( Throwable $e )
		{
			$this->getEnv()->getErrorHandler()->captureException( $e );

			return 1;
		}

		return 0;
	}
}