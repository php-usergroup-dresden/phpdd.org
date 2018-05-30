<?php declare(strict_types=1);

namespace PHPUGDD\PHPDD\Website\Tickets\Application\Tickets;

use PHPUGDD\PHPDD\Website\Tickets\Application\Constants\TicketTypes;
use PHPUGDD\PHPDD\Website\Tickets\Application\Tickets\Exceptions\AllowedTicketCountExceededException;
use PHPUGDD\PHPDD\Website\Tickets\Application\Tickets\Exceptions\AllowedTicketCountPerAttendeeExceededException;
use PHPUGDD\PHPDD\Website\Tickets\Application\Tickets\Interfaces\CollectsTicketItems;
use PHPUGDD\PHPDD\Website\Tickets\Application\Tickets\Interfaces\ProvidesTicketOrderInformation;
use PHPUGDD\PHPDD\Website\Tickets\Application\Types\DiversityDonation;
use PHPUGDD\PHPDD\Website\Tickets\Application\Types\TicketOrderDate;
use PHPUGDD\PHPDD\Website\Tickets\Application\Types\TicketOrderDiscountTotal;
use PHPUGDD\PHPDD\Website\Tickets\Application\Types\TicketOrderEmailAddress;
use PHPUGDD\PHPDD\Website\Tickets\Application\Types\TicketOrderId;
use PHPUGDD\PHPDD\Website\Tickets\Application\Types\TicketOrderPaymentTotal;
use PHPUGDD\PHPDD\Website\Tickets\Application\Types\TicketOrderTotal;
use PHPUGDD\PHPDD\Website\Tickets\Application\Types\TicketType;
use PHPUGDD\PHPDD\Website\Tickets\Traits\MoneyProviding;

final class TicketOrder implements ProvidesTicketOrderInformation
{
	use MoneyProviding;

	private const WORKSHOP_TICKETS_MAX               = 10;

	private const WORKSHOP_SLOT_TICKETS_PER_ATTENDEE = 1;

	private const CONFERENCE_TICKETS_MAX             = 10;

	private const CONFERENCE_TICKETS_PER_ATTENDEE    = 1;

	/** @var TicketOrderId */
	private $orderId;

	/** @var TicketOrderDate */
	private $orderDate;

	/** @var CollectsTicketItems */
	private $ticketItems;

	/** @var null|TicketOrderEmailAddress */
	private $emailAddress;

	/** @var null|TicketOrderBillingAddress */
	private $billingAddress;

	/** @var null|DiversityDonation */
	private $diversityDonation;

	/**
	 * @param TicketOrderId   $orderId
	 * @param TicketOrderDate $orderDate
	 */
	public function __construct( TicketOrderId $orderId, TicketOrderDate $orderDate )
	{
		$this->orderId     = $orderId;
		$this->orderDate   = $orderDate;
		$this->ticketItems = new TicketItemCollection();
	}

	public function isPlaceable() : bool
	{
		if ( null === $this->emailAddress )
		{
			return false;
		}

		if ( null === $this->billingAddress )
		{
			return false;
		}

		if ( 0 === $this->ticketItems->count() )
		{
			return false;
		}

		return true;
	}

	public function sendTicketsAndInvoiceTo( TicketOrderEmailAddress $emailAddress ) : void
	{
		$this->emailAddress = $emailAddress;
	}

	public function billTo( TicketOrderBillingAddress $billingAddress ) : void
	{
		$this->billingAddress = $billingAddress;
	}

	/**
	 * @param TicketItem $ticketItem
	 * @param TicketItem ...$ticketItems
	 *
	 * @throws AllowedTicketCountExceededException
	 * @throws AllowedTicketCountPerAttendeeExceededException
	 */
	public function orderTickets( TicketItem $ticketItem, TicketItem ...$ticketItems ) : void
	{
		$this->orderTicket( $ticketItem );

		foreach ( $ticketItems as $ticketItemElement )
		{
			$this->orderTicket( $ticketItemElement );
		}
	}

	/**
	 * @param TicketItem $ticketItem
	 *
	 * @throws AllowedTicketCountExceededException
	 * @throws AllowedTicketCountPerAttendeeExceededException
	 */
	private function orderTicket( TicketItem $ticketItem ) : void
	{
		$ticketType      = $ticketItem->getTicket()->getType();
		$ticketCountType = $this->ticketItems->getCountForType( $ticketType );
		$maxCountType    = $this->getMaxCountForTicketType( $ticketType );

		if ( $ticketCountType >= $maxCountType )
		{
			throw new AllowedTicketCountExceededException(
				sprintf( 'Allowed ticket count of %d exceeded.', $maxCountType )
			);
		}

		$attendeeName               = $ticketItem->getAttendeeName();
		$ticketCountTypeForAttendee = $this->ticketItems->getCountForTypeAndAttendeeName( $ticketType, $attendeeName );
		$maxCountTypeForAttendee    = $this->getMaxCountForTicketTypePerAttendee( $ticketType );

		if ( $ticketCountTypeForAttendee >= $maxCountTypeForAttendee )
		{
			throw new AllowedTicketCountPerAttendeeExceededException(
				sprintf(
					'Allowed ticket count of %d for attendee %s exceeded.',
					$maxCountTypeForAttendee,
					$attendeeName->toString()
				)
			);
		}

		$this->ticketItems->add( $ticketItem );
	}

	private function getMaxCountForTicketType( TicketType $ticketType ) : int
	{
		$maxCount = self::CONFERENCE_TICKETS_MAX;

		if ( \in_array( $ticketType->toString(), TicketTypes::WORKSHOPS, true ) )
		{
			$maxCount = self::WORKSHOP_TICKETS_MAX;
		}

		return $maxCount;
	}

	private function getMaxCountForTicketTypePerAttendee( TicketType $ticketType ) : int
	{
		$maxCount = self::CONFERENCE_TICKETS_PER_ATTENDEE;

		if ( \in_array( $ticketType->toString(), TicketTypes::WORKSHOPS, true ) )
		{
			$maxCount = self::WORKSHOP_SLOT_TICKETS_PER_ATTENDEE;
		}

		return $maxCount;
	}

	public function getOrderId() : TicketOrderId
	{
		return $this->orderId;
	}

	public function getOrderDate() : TicketOrderDate
	{
		return $this->orderDate;
	}

	public function getTicketItems() : CollectsTicketItems
	{
		return $this->ticketItems;
	}

	public function getDiscountItems() : DiscountItemCollection
	{
		$discountItems = new DiscountItemCollection();
		foreach ( $this->ticketItems as $ticketItem )
		{
			$discountItem = $ticketItem->getDiscountItem();

			if ( null !== $discountItem )
			{
				$discountItems->add( $discountItem );
			}
		}

		return $discountItems;
	}

	public function getEmailAddress() : ?TicketOrderEmailAddress
	{
		return $this->emailAddress;
	}

	public function getBillingAddress() : ?TicketOrderBillingAddress
	{
		return $this->billingAddress;
	}

	public function donateToDiversity( DiversityDonation $diversityDonation ) : void
	{
		$this->diversityDonation = $diversityDonation;
	}

	public function getDiversityDonation() : ?DiversityDonation
	{
		return $this->diversityDonation;
	}

	/**
	 * @return TicketOrderTotal
	 * @throws \InvalidArgumentException
	 */
	public function getOrderTotal() : TicketOrderTotal
	{
		$money = $this->getMoney( 0 );

		foreach ( $this->ticketItems as $ticketItem )
		{
			$money = $money->add( $ticketItem->getTicket()->getPrice()->getMoney() );
		}

		return new TicketOrderTotal( $money );
	}

	/**
	 * @return TicketOrderDiscountTotal
	 * @throws \InvalidArgumentException
	 */
	public function getDiscountTotal() : TicketOrderDiscountTotal
	{
		$money = $this->getMoney( 0 );

		foreach ( $this->ticketItems as $ticketItem )
		{
			if ( null === $ticketItem->getDiscountItem() )
			{
				continue;
			}

			$money = $money->add( $ticketItem->getDiscountItem()->getDiscountPrice()->getMoney() );
		}

		return new TicketOrderDiscountTotal( $money );
	}

	/**
	 * @return TicketOrderPaymentTotal
	 * @throws \InvalidArgumentException
	 */
	public function getPaymentTotal() : TicketOrderPaymentTotal
	{
		$orderTotal    = $this->getOrderTotal();
		$discountTotal = $this->getDiscountTotal();
		$money         = $orderTotal->getMoney()->subtract( $discountTotal->getMoney()->absolute() );

		return new TicketOrderPaymentTotal( $money );
	}
}
