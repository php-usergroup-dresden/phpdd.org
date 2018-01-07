<?php declare(strict_types=1);
/**
 * @author hollodotme
 */

namespace PHPUGDD\PHPDD\Website\Application\Tickets;

use PHPUGDD\PHPDD\Website\Application\Constants\CountryCodes;
use PHPUGDD\PHPDD\Website\Application\Constants\TicketTypes;
use PHPUGDD\PHPDD\Website\Application\Tickets\Exceptions\AllowedTicketCountExceededException;
use PHPUGDD\PHPDD\Website\Application\Tickets\Exceptions\AllowedTicketCountPerAttendeeExceededException;
use PHPUGDD\PHPDD\Website\Application\Tickets\Interfaces\ProvidesTicketOrderInformation;
use PHPUGDD\PHPDD\Website\Application\Types\AddressAddon;
use PHPUGDD\PHPDD\Website\Application\Types\City;
use PHPUGDD\PHPDD\Website\Application\Types\CompanyName;
use PHPUGDD\PHPDD\Website\Application\Types\CountryCode;
use PHPUGDD\PHPDD\Website\Application\Types\DiversityDonation;
use PHPUGDD\PHPDD\Website\Application\Types\Firstname;
use PHPUGDD\PHPDD\Website\Application\Types\Lastname;
use PHPUGDD\PHPDD\Website\Application\Types\StreetWithNumber;
use PHPUGDD\PHPDD\Website\Application\Types\TicketOrderDate;
use PHPUGDD\PHPDD\Website\Application\Types\TicketOrderDiscountTotal;
use PHPUGDD\PHPDD\Website\Application\Types\TicketOrderEmailAddress;
use PHPUGDD\PHPDD\Website\Application\Types\TicketOrderId;
use PHPUGDD\PHPDD\Website\Application\Types\TicketOrderPaymentTotal;
use PHPUGDD\PHPDD\Website\Application\Types\TicketOrderTotal;
use PHPUGDD\PHPDD\Website\Application\Types\TicketType;
use PHPUGDD\PHPDD\Website\Application\Types\ZipCode;
use PHPUGDD\PHPDD\Website\Traits\MoneyProviding;

/**
 * Class TicketOrder
 * @package PHPUGDD\PHPDD\Website\Application\Tickets
 */
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

	/** @var TicketItemCollection */
	private $ticketItems;

	/** @var TicketOrderEmailAddress */
	private $emailAddress;

	/** @var TicketOrderBillingAddress */
	private $billingAddress;

	/** @var DiversityDonation */
	private $diversityDonation;

	/**
	 * @param TicketOrderId   $orderId
	 * @param TicketOrderDate $orderDate
	 *
	 * @throws \InvalidArgumentException
	 */
	public function __construct( TicketOrderId $orderId, TicketOrderDate $orderDate )
	{
		$this->orderId           = $orderId;
		$this->orderDate         = $orderDate;
		$this->ticketItems       = new TicketItemCollection();
		$this->emailAddress      = new TicketOrderEmailAddress( 'you@example.com' );
		$this->billingAddress    = new TicketOrderBillingAddress(
			new Firstname( '' ),
			new Lastname( '' ),
			new CompanyName( '' ),
			new StreetWithNumber( '' ),
			new AddressAddon( '' ),
			new ZipCode( '' ),
			new City( '' ),
			new CountryCode( CountryCodes::DE_SHORT )
		);
		$this->diversityDonation = new DiversityDonation( $this->getMoney( 0 ) );
	}

	/**
	 * @param TicketItem[] ...$ticketItems
	 *
	 * @throws AllowedTicketCountExceededException
	 * @throws AllowedTicketCountPerAttendeeExceededException
	 */
	public function orderTickets( TicketItem ...$ticketItems ) : void
	{
		foreach ( $ticketItems as $ticketItem )
		{
			$this->orderTicket( $ticketItem );
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
			throw new AllowedTicketCountExceededException( sprintf( 'Allowed ticket count of %d exceeded.', $maxCountType ) );
		}

		$attendeeName               = $ticketItem->getAttendeeName();
		$ticketCountTypeForAttendee = $this->ticketItems->getCountForTypeAndAttendeeName( $ticketType, $attendeeName );
		$maxCountTypeForAttendee    = $this->getMaxCountForTicketTypePerAttendee( $ticketType );

		if ( $ticketCountTypeForAttendee >= $maxCountTypeForAttendee )
		{
			throw new AllowedTicketCountPerAttendeeExceededException(
				sprintf( 'Allowed ticket count of %d for attendee %s exceeded.', $maxCountTypeForAttendee, $attendeeName->toString() )
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

	public function getTicketItems() : TicketItemCollection
	{
		return $this->ticketItems;
	}

	public function getDiscountItems() : DiscountItemCollection
	{
		$discountItems = new DiscountItemCollection();
		foreach ( $this->ticketItems as $ticketItem )
		{
			$discountItems->add( $ticketItem->getDiscountItem() );
		}

		return $discountItems;
	}

	public function getEmailAddress() : TicketOrderEmailAddress
	{
		return $this->emailAddress;
	}

	public function getBillingAddress() : TicketOrderBillingAddress
	{
		return $this->billingAddress;
	}

	public function getDiversityDonation() : DiversityDonation
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
