<?php declare(strict_types=1);
/**
 * @author hollodotme
 */

namespace PHPUGDD\PHPDD\Website\Tickets\Application\Tickets\Interfaces;

use PHPUGDD\PHPDD\Website\Tickets\Application\Tickets\DiscountItem;
use PHPUGDD\PHPDD\Website\Tickets\Application\Tickets\Ticket;
use PHPUGDD\PHPDD\Website\Tickets\Application\Types\AttendeeName;

/**
 * Class TicketItem
 * @package PHPUGDD\PHPDD\Website\Tickets\Application\Tickets
 */
interface ProvidesTicketItemInformation
{
	public function getTicket() : Ticket;

	public function getAttendeeName() : AttendeeName;

	public function getDiscountItem() : ?DiscountItem;
}
