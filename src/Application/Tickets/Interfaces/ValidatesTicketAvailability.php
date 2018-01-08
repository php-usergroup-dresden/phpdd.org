<?php declare(strict_types=1);
/**
 * @author hollodotme
 */

namespace PHPUGDD\PHPDD\Website\Application\Tickets\Interfaces;

/**
 * Interface ProvidesTicketAvailability
 * @package PHPUGDD\PHPDD\Website\Application\Tickets\Interfaces
 */
interface ValidatesTicketAvailability
{
	public function isAvailable( ProvidesTicketItemInformation $ticketItem ) : bool;
}
