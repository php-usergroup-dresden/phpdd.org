<?php declare(strict_types=1);
/**
 * @author hollodotme
 */

namespace PHPUGDD\PHPDD\Website\Application\Tickets\Interfaces;

/**
 * Interface ValidatesTicketRules
 * @package PHPUGDD\PHPDD\Website\Application\Tickets\Interfaces
 */
interface ValidatesTicketOrderRules
{
	public function canOrderTicket( ProvidesTicketItemInformation $ticketItem ) : bool;
}
