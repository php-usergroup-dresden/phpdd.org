<?php declare(strict_types=1);
/**
 * @author hollodotme
 */

namespace PHPUGDD\PHPDD\Website\Application\Tickets;

use PHPUGDD\PHPDD\Website\Application\Configs\Exceptions\TicketConfigNotFoundException;
use PHPUGDD\PHPDD\Website\Application\Configs\TicketsConfig;
use PHPUGDD\PHPDD\Website\Application\Tickets\Interfaces\CollectsTicketItems;
use PHPUGDD\PHPDD\Website\Application\Tickets\Interfaces\ProvidesReservedTicketCount;
use PHPUGDD\PHPDD\Website\Application\Tickets\Interfaces\ProvidesTicketItemInformation;
use PHPUGDD\PHPDD\Website\Application\Tickets\Interfaces\ValidatesTicketAvailability;

/**
 * Class TicketAvailabilityValidator
 * @package PHPUGDD\PHPDD\Website\Application\Tickets
 */
final class TicketAvailabilityValidator implements ValidatesTicketAvailability
{
	/** @var TicketsConfig */
	private $ticketsConfig;

	/** @var ProvidesReservedTicketCount */
	private $reservedTicketCounts;

	/** @var CollectsTicketItems */
	private $ticketItems;

	public function __construct( TicketsConfig $ticketsConfig, ProvidesReservedTicketCount $reservedTicketCounts, CollectsTicketItems $ticketItems )
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
			$ticketConfig = $this->ticketsConfig->findTicketConfigByTypeAndName( $ticket->getType(), $ticket->getName() );
		}
		catch ( TicketConfigNotFoundException $e )
		{
			return false;
		}

		$seats         = $ticketConfig->getSeats();
		$ordered       = $this->ticketItems->getCountForTicket( $ticket );
		$reservedCount = $this->reservedTicketCounts->getReservedCount( $ticket );

		$availability = $seats - $ordered - $reservedCount;

		return $availability > 0;
	}
}
