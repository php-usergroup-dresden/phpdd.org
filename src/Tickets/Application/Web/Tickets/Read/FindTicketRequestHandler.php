<?php declare(strict_types=1);

namespace PHPUGDD\PHPDD\Website\Tickets\Application\Web\Tickets\Read;

use IceHawk\IceHawk\Interfaces\HandlesGetRequest;
use IceHawk\IceHawk\Interfaces\ProvidesReadRequestData;
use PHPUGDD\PHPDD\Website\Tickets\Application\Configs\Exceptions\TicketConfigNotFoundException;
use PHPUGDD\PHPDD\Website\Tickets\Application\Configs\TicketsConfig;
use PHPUGDD\PHPDD\Website\Tickets\Application\Tickets\Repositories\TicketOrderRepository;
use PHPUGDD\PHPDD\Website\Tickets\Application\Types\TicketId;
use PHPUGDD\PHPDD\Website\Tickets\Application\Types\TicketItemId;
use PHPUGDD\PHPDD\Website\Tickets\Application\Web\AbstractRequestHandler;
use PHPUGDD\PHPDD\Website\Tickets\Application\Web\Responses\HtmlPage;
use RuntimeException;

final class FindTicketRequestHandler extends AbstractRequestHandler implements HandlesGetRequest
{
	/**
	 * @param ProvidesReadRequestData $request
	 *
	 * @throws \PHPUGDD\PHPDD\Website\Tickets\Application\Exceptions\RuntimeException
	 * @throws \Twig_Error_Loader
	 * @throws \Twig_Error_Runtime
	 * @throws \Twig_Error_Syntax
	 * @throws \Fortuneglobe\Types\Exceptions\InvalidArgumentException
	 */
	public function handle( ProvidesReadRequestData $request )
	{
		$ticketItemId = trim( $request->getInput()->get( 'ticketItemId' ) );
		$data         = ['ticketNotFound' => false];

		if ( null === $ticketItemId )
		{
			$data['ticketNotFound'] = true;
			(new HtmlPage( $this->getEnv() ))->respond( 'Tickets/Read/Pages/FindTicket.twig', $data );

			return;
		}

		$database      = $this->getEnv()->getDatabase();
		$repository    = new TicketOrderRepository( $database );
		$ticketsConfig = TicketsConfig::fromConfigFile();

		try
		{
			$ticketItem   = $repository->getTicketItem( new TicketItemId( $ticketItemId ) );
			$ticketConfig = $ticketsConfig->findTicketById( new TicketId( $ticketItem->ticketId ) );

			$data = [
				'attendeeName'    => $ticketItem->attendeeName,
				'ticketName'      => $ticketConfig->getName(),
				'ticketScanned'   => 'Y' === $ticketItem->scanned,
				'ticketScannedAt' => $ticketItem->scannedAt,
			];

			(new HtmlPage( $this->getEnv() ))->respond( 'Tickets/Read/Pages/FindTicket.twig', $data );
		}
		catch ( RuntimeException | TicketConfigNotFoundException $e )
		{
			$data['ticketNotFound'] = true;
			(new HtmlPage( $this->getEnv() ))->respond( 'Tickets/Read/Pages/FindTicket.twig', $data );
		}
	}
}