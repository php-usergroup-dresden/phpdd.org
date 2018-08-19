<?php declare(strict_types=1);

namespace PHPUGDD\PHPDD\Website\Tickets\Application\Tickets;

use Money\Money;
use PHPUGDD\PHPDD\Website\Tickets\Application\Exceptions\LogicException;
use PHPUGDD\PHPDD\Website\Tickets\Application\Tickets\Exceptions\DiscountExceededTicketPriceException;
use PHPUGDD\PHPDD\Website\Tickets\Application\Tickets\Exceptions\DiscountNotAllowedForTicketException;
use PHPUGDD\PHPDD\Website\Tickets\Application\Tickets\Interfaces\ProvidesTicketItemInformation;
use PHPUGDD\PHPDD\Website\Tickets\Application\Types\AttendeeName;
use PHPUGDD\PHPDD\Website\Tickets\Traits\MoneyProviding;

final class TicketItem implements ProvidesTicketItemInformation
{
	use MoneyProviding;

	/** @var Ticket */
	private $ticket;

	/** @var AttendeeName */
	private $attendeeName;

	/** @var null|DiscountItem */
	private $discountItem;

	/** @var string */
	private $status;

	/**
	 * @param Ticket       $ticket
	 * @param AttendeeName $attendeeName
	 */
	public function __construct( Ticket $ticket, AttendeeName $attendeeName )
	{
		$this->ticket       = $ticket;
		$this->attendeeName = $attendeeName;
		$this->status       = TicketItemStatus::PAID;
	}

	/**
	 * @param DiscountItem $discountItem
	 *
	 * @throws DiscountExceededTicketPriceException
	 * @throws DiscountNotAllowedForTicketException
	 */
	public function grantDiscount( DiscountItem $discountItem ) : void
	{
		$this->guardDiscountMoneyIsValid( $discountItem->getDiscountPrice()->getMoney() );
		$this->guardTicketIsAllowedForDiscount( $discountItem );

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

	/**
	 * @param DiscountItem $discountItem
	 *
	 * @throws DiscountNotAllowedForTicketException
	 */
	private function guardTicketIsAllowedForDiscount( DiscountItem $discountItem ) : void
	{
		if ( !$discountItem->isAllowedForTicket( $this->ticket->getId() ) )
		{
			throw new DiscountNotAllowedForTicketException(
				sprintf(
					'Discount "%s" is not allowed for ticket "%s".',
					$discountItem->getName(),
					$this->ticket->getId()
				)
			);
		}
	}

	/**
	 * @throws LogicException
	 */
	public function refund() : void
	{
		if ( TicketItemStatus::REFUNDED === $this->status )
		{
			throw new LogicException( 'Ticket item is already refunded.' );
		}

		$this->status = TicketItemStatus::REFUNDED;
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

	public function getStatus() : string
	{
		return $this->status;
	}
}
