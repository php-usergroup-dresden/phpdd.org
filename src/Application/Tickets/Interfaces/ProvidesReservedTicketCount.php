<?php declare(strict_types=1);
/**
 * @author hollodotme
 */

namespace PHPUGDD\PHPDD\Website\Application\Tickets\Interfaces;

use PHPUGDD\PHPDD\Website\Application\Tickets\Ticket;

/**
 * Interface ProvidesReservedTicketCount
 * @package PHPUGDD\PHPDD\Website\Application\Tickets\Interfaces
 */
interface ProvidesReservedTicketCount
{
	public function getReservedCount( Ticket $ticket ) : int;
}
