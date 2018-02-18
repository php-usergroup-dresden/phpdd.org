<?php declare(strict_types=1);
/**
 * @author hollodotme
 */

namespace PHPUGDD\PHPDD\Website\Tickets\Application\Tickets\Interfaces;

use PHPUGDD\PHPDD\Website\Tickets\Application\Tickets\Ticket;
use PHPUGDD\PHPDD\Website\Tickets\Application\Tickets\TicketItem;
use PHPUGDD\PHPDD\Website\Tickets\Application\Types\AttendeeName;
use PHPUGDD\PHPDD\Website\Tickets\Application\Types\TicketType;

/**
 * Class TicketItemCollection
 * @package PHPUGDD\PHPDD\Website\Tickets\Application\Tickets
 */
interface CollectsTicketItems extends \Countable, \Iterator
{
	public function add( TicketItem $ticketItem ) : void;

	public function getCountForType( TicketType $ticketType ) : int;

	public function getCountForTicket( Ticket $ticket ) : int;

	public function getCountForTypeAndAttendeeName( TicketType $ticketType, AttendeeName $attendeeName ) : int;
}
