<?php declare(strict_types=1);
/**
 * @author hollodotme
 */

namespace PHPUGDD\PHPDD\Website\Application\Tickets;

/**
 * Class DiscountItemCollection
 * @package PHPUGDD\PHPDD\Website\Application\Tickets
 */
final class DiscountItemCollection implements \Countable, \Iterator
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
		return \count( $this->discountItems );
	}
}
