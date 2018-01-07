<?php declare(strict_types=1);
/**
 * @author hollodotme
 */

namespace PHPUGDD\PHPDD\Website\Application\Tickets;

use Fortuneglobe\Types\Exceptions\InvalidArgumentException;
use Money\Money;
use PHPUGDD\PHPDD\Website\Application\Tickets\Exceptions\DiscountExceededTicketPriceException;
use PHPUGDD\PHPDD\Website\Application\Types\AttendeeName;
use PHPUGDD\PHPDD\Website\Application\Types\DiscountCode;
use PHPUGDD\PHPDD\Website\Application\Types\DiscountDescription;
use PHPUGDD\PHPDD\Website\Application\Types\DiscountName;
use PHPUGDD\PHPDD\Website\Application\Types\DiscountPrice;
use PHPUGDD\PHPDD\Website\Traits\MoneyProviding;

/**
 * Class TicketItem
 * @package PHPUGDD\PHPDD\Website\Application\Tickets
 */
final class TicketItem
{
	use MoneyProviding;

	/** @var Ticket */
	private $ticket;

	/** @var AttendeeName */
	private $attendeeName;

	/** @var DiscountItem */
	private $discountItem;

	/**
	 * @param Ticket       $ticket
	 * @param AttendeeName $attendeeName
	 *
	 * @throws \InvalidArgumentException
	 * @throws InvalidArgumentException
	 */
	public function __construct( Ticket $ticket, AttendeeName $attendeeName )
	{
		$this->ticket       = $ticket;
		$this->attendeeName = $attendeeName;
		$this->discountItem = new DiscountItem(
			new DiscountName( '' ),
			new DiscountCode( '0OOOOOO0' ),
			new DiscountDescription( '' ),
			new DiscountPrice( $this->getMoney( 0 ) )
		);
	}

	/**
	 * @param DiscountItem $discountItem
	 *
	 * @throws DiscountExceededTicketPriceException
	 */
	public function grantDiscount( DiscountItem $discountItem ) : void
	{
		$this->guardDiscountMoneyIsValid( $discountItem->getDiscountPrice()->getMoney() );

		$this->discountItem = $discountItem;
	}

	/**
	 * @param Money $money
	 *
	 * @throws DiscountExceededTicketPriceException
	 */
	private function guardDiscountMoneyIsValid( Money $money ) : void
	{
		if ( $money->absolute()->greaterThan( $this->ticket->getPrice()->getMoney() ) )
		{
			throw new DiscountExceededTicketPriceException( 'Discount exceeded ticket price.' );
		}
	}

	public function getTicket() : Ticket
	{
		return $this->ticket;
	}

	public function getAttendeeName() : AttendeeName
	{
		return $this->attendeeName;
	}

	public function getDiscountItem() : DiscountItem
	{
		return $this->discountItem;
	}
}
