<?php declare(strict_types=1);

namespace PHPUGDD\PHPDD\Website\Tickets\Application\Tickets;

use PHPUGDD\PHPDD\Website\Tickets\Application\Configs\Exceptions\TicketConfigNotFoundException;
use PHPUGDD\PHPDD\Website\Tickets\Application\Configs\TicketsConfig;
use PHPUGDD\PHPDD\Website\Tickets\Application\Tickets\Interfaces\CollectsTicketItems;
use PHPUGDD\PHPDD\Website\Tickets\Application\Tickets\Interfaces\ProvidesReservedTicketCount;
use PHPUGDD\PHPDD\Website\Tickets\Application\Tickets\Interfaces\ProvidesTicketItemInformation;
use PHPUGDD\PHPDD\Website\Tickets\Application\Tickets\Interfaces\ValidatesTicketAvailability;

final class TicketAvailabilityValidator implements ValidatesTicketAvailability
{
	/** @var TicketsConfig */
	private $ticketsConfig;

	/** @var ProvidesReservedTicketCount */
	private $reservedTicketCounts;

	/** @var CollectsTicketItems */
	private $ticketItems;

	public function __construct(
		TicketsConfig $ticketsConfig,
		ProvidesReservedTicketCount $reservedTicketCounts,
		CollectsTicketItems $ticketItems
	)
	{
		$this->ticketsConfig        = $ticketsConfig;
		$this->reservedTicketCounts = $reservedTicketCounts;
		$this->ticketItems          = $ticketItems;
	}

	public function isAvailable( ProvidesTicketItemInformation $ticketItem ) : bool
	{
		$ticket = $ticketItem->getTicket();

		try
		{
			$ticketConfig = $this->ticketsConfig->findTicketById( $ticket->getId() );
		}
		catch ( TicketConfigNotFoundException $e )
		{
			return false;
		}

		$seats         = $ticketConfig->getSeats();
		$ordered       = $this->ticketItems->getCountForTicket( $ticket );
		$reservedCount = $this->reservedTicketCounts->getReservedCount( $ticket );

		$availability = $seats - $ordered - $reservedCount;

		return $availability >= 0;
	}
}
