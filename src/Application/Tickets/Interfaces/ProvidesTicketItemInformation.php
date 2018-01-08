<?php declare(strict_types=1);
/**
 * @author hollodotme
 */

namespace PHPUGDD\PHPDD\Website\Application\Tickets\Interfaces;

use PHPUGDD\PHPDD\Website\Application\Tickets\DiscountItem;
use PHPUGDD\PHPDD\Website\Application\Tickets\Ticket;
use PHPUGDD\PHPDD\Website\Application\Types\AttendeeName;

/**
 * Class TicketItem
 * @package PHPUGDD\PHPDD\Website\Application\Tickets
 */
interface ProvidesTicketItemInformation
{
	public function getTicket() : Ticket;

	public function getAttendeeName() : AttendeeName;

	public function getDiscountItem() : DiscountItem;
}
