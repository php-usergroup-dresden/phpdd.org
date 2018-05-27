<?php declare(strict_types=1);

namespace PHPUGDD\PHPDD\Website\Tickets\Application\Tickets;

use Generator;
use PHPUGDD\PHPDD\Website\Tickets\Application\Configs\TicketsConfig;
use PHPUGDD\PHPDD\Website\Tickets\Application\Tickets\Interfaces\ProvidesReservedTicketCount;

final class TicketInfos
{
	/** @var TicketsConfig */
	private $ticketsConfig;

	/** @var ProvidesReservedTicketCount */
	private $reservationService;

	/**
	 * @param TicketsConfig               $ticketsConfig
	 * @param ProvidesReservedTicketCount $reservationService
	 */
	public function __construct( TicketsConfig $ticketsConfig, ProvidesReservedTicketCount $reservationService )
	{
		$this->ticketsConfig      = $ticketsConfig;
		$this->reservationService = $reservationService;
	}

	/**
	 * @throws \InvalidArgumentException
	 * @return Generator
	 */
	public function getTickets() : Generator
	{
		foreach ( $this->ticketsConfig->getTicketConfigs() as $ticketConfig )
		{
			$ticket = new Ticket(
				$ticketConfig->getId(),
				$ticketConfig->getType(),
				$ticketConfig->getName(),
				$ticketConfig->getDescription(),
				$ticketConfig->getPrice()
			);

			$reservedCount = $this->reservationService->getReservedCount( $ticket );

			yield new TicketInfo( $ticketConfig, $reservedCount );
		}
	}
}