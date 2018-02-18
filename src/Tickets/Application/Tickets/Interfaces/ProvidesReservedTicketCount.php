<?php declare(strict_types=1);
/**
 * @author hollodotme
 */

namespace PHPUGDD\PHPDD\Website\Tickets\Application\Tickets\Interfaces;

use PHPUGDD\PHPDD\Website\Tickets\Application\Tickets\Ticket;

/**
 * Interface ProvidesReservedTicketCount
 * @package PHPUGDD\PHPDD\Website\Tickets\Application\Tickets\Interfaces
 */
interface ProvidesReservedTicketCount
{
	public function getReservedCount( Ticket $ticket ) : int;
}
