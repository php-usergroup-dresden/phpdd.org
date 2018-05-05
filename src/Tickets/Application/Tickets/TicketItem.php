<?php declare(strict_types=1);
/**
 * @author hollodotme
 */

namespace PHPUGDD\PHPDD\Website\Tickets\Application\Tickets;

use Money\Money;
use PHPUGDD\PHPDD\Website\Tickets\Application\Tickets\Exceptions\DiscountExceededTicketPriceException;
use PHPUGDD\PHPDD\Website\Tickets\Application\Tickets\Interfaces\ProvidesTicketItemInformation;
use PHPUGDD\PHPDD\Website\Tickets\Application\Types\AttendeeName;
use PHPUGDD\PHPDD\Website\Tickets\Traits\MoneyProviding;

/**
 * Class TicketItem
 * @package PHPUGDD\PHPDD\Website\Tickets\Application\Tickets
 */
final class TicketItem implements ProvidesTicketItemInformation
{
	use MoneyProviding;

	/** @var Ticket */
	private $ticket;

	/** @var AttendeeName */
	private $attendeeName;

	/** @var null|DiscountItem */
	private $discountItem;

	/**
	 * @param Ticket       $ticket
	 * @param AttendeeName $attendeeName
	 */
	public function __construct( Ticket $ticket, AttendeeName $attendeeName )
	{
		$this->ticket       = $ticket;
		$this->attendeeName = $attendeeName;
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

	public function getDiscountItem() : ?DiscountItem
	{
		return $this->discountItem;
	}
}
