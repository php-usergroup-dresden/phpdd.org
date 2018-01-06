<?php declare(strict_types=1);
/**
 * @author hollodotme
 */

namespace PHPUGDD\PHPDD\Website\Application\Tickets;

use PHPUGDD\PHPDD\Website\Application\Types\TicketType;

/**
 * Class TicketItemCollection
 * @package PHPUGDD\PHPDD\Website\Application\Tickets
 */
final class TicketItemCollection implements \Countable, \Iterator
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
