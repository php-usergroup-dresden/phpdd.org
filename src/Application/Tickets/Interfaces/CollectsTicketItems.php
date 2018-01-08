<?php declare(strict_types=1);
/**
 * @author hollodotme
 */

namespace PHPUGDD\PHPDD\Website\Application\Tickets\Interfaces;

use PHPUGDD\PHPDD\Website\Application\Tickets\Ticket;
use PHPUGDD\PHPDD\Website\Application\Tickets\TicketItem;
use PHPUGDD\PHPDD\Website\Application\Types\AttendeeName;
use PHPUGDD\PHPDD\Website\Application\Types\TicketType;

/**
 * Class TicketItemCollection
 * @package PHPUGDD\PHPDD\Website\Application\Tickets
 */
interface CollectsTicketItems extends \Countable, \Iterator
{
	public function add( TicketItem $ticketItem ) : void;

	public function getCountForType( TicketType $ticketType ) : int;

	public function getCountForTicket( Ticket $ticket ) : int;

	public function getCountForTypeAndAttendeeName( TicketType $ticketType, AttendeeName $attendeeName ) : int;
}
