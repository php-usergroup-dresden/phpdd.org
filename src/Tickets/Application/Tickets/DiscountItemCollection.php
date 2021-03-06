<?php declare(strict_types=1);

namespace PHPUGDD\PHPDD\Website\Tickets\Application\Tickets;

use Countable;
use Iterator;
use function count;
use function current;
use function key;
use function next;

final class DiscountItemCollection implements Countable, Iterator
{
	/** @var array|DiscountItem[] */
	private $discountItems = [];

	public function add( DiscountItem $discountItem ) : void
	{
		$this->discountItems[] = $discountItem;
	}

	public function current() : DiscountItem
	{
		return current( $this->discountItems );
	}

	public function next() : void
	{
		next( $this->discountItems );
	}

	public function key() : int
	{
		return key( $this->discountItems );
	}

	public function valid() : bool
	{
		return null !== key( $this->discountItems );
	}

	public function rewind() : void
	{
		reset( $this->discountItems );
	}

	public function count() : int
	{
		return count( $this->discountItems );
	}
}
