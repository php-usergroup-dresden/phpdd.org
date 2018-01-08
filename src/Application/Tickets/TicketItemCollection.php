<?php declare(strict_types=1);
/**
 * @author hollodotme
 */

namespace PHPUGDD\PHPDD\Website\Application\Tickets;

use PHPUGDD\PHPDD\Website\Application\Tickets\Interfaces\CollectsTicketItems;
use PHPUGDD\PHPDD\Website\Application\Types\AttendeeName;
use PHPUGDD\PHPDD\Website\Application\Types\TicketType;

/**
 * Class TicketItemCollection
 * @package PHPUGDD\PHPDD\Website\Application\Tickets
 */
final class TicketItemCollection implements CollectsTicketItems
{
	/** @var array|TicketItem[] */
	private $ticketItems = [];

	public function add( TicketItem $ticketItem ) : void
	{
		$this->ticketItems[] = $ticketItem;
	}

	public function getCountForType( TicketType $ticketType ) : int
	{
		$count = 0;

		foreach ( $this->ticketItems as $ticketItem )
		{
			if ( $ticketType->equals( $ticketItem->getTicket()->getType() ) )
			{
				$count++;
			}
		}

		return $count;
	}

	public function getCountForTicket( Ticket $ticket ) : int
	{
		$count = 0;

		foreach ( $this->ticketItems as $ticketItem )
		{
			if ( !$ticket->getType()->equals( $ticketItem->getTicket()->getType() ) )
			{
				continue;
			}

			if ( !$ticket->getName()->equals( $ticketItem->getTicket()->getName() ) )
			{
				continue;
			}

			$count++;
		}

		return $count;
	}

	public function getCountForTypeAndAttendeeName( TicketType $ticketType, AttendeeName $attendeeName ) : int
	{
		$count = 0;

		foreach ( $this->ticketItems as $ticketItem )
		{
			if ( !$ticketType->equals( $ticketItem->getTicket()->getType() ) )
			{
				continue;
			}

			if ( !$attendeeName->equals( $ticketItem->getAttendeeName() ) )
			{
				continue;
			}

			$count++;
		}

		return $count;
	}

	public function current() : TicketItem
	{
		return current( $this->ticketItems );
	}

	public function next() : void
	{
		next( $this->ticketItems );
	}

	public function key() : int
	{
		return key( $this->ticketItems );
	}

	public function valid() : bool
	{
		return null !== key( $this->ticketItems );
	}

	public function rewind() : void
	{
		reset( $this->ticketItems );
	}

	public function count() : int
	{
		return \count( $this->ticketItems );
	}
}
