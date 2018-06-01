<?php declare(strict_types=1);

namespace PHPUGDD\PHPDD\Website\Tickets\Application\Tickets;

use PHPUGDD\PHPDD\Website\Tickets\Application\Constants\CountryCodes;
use PHPUGDD\PHPDD\Website\Tickets\Application\Constants\TicketTypes;
use PHPUGDD\PHPDD\Website\Tickets\Application\Exceptions\InvalidArgumentException;
use PHPUGDD\PHPDD\Website\Tickets\Application\Payments\Interfaces\CalculatesPaymentFee;
use PHPUGDD\PHPDD\Website\Tickets\Application\Tickets\Exceptions\AllowedTicketCountExceededException;
use PHPUGDD\PHPDD\Website\Tickets\Application\Tickets\Exceptions\AllowedTicketCountPerAttendeeExceededException;
use PHPUGDD\PHPDD\Website\Tickets\Application\Tickets\Interfaces\CollectsTicketItems;
use PHPUGDD\PHPDD\Website\Tickets\Application\Tickets\Interfaces\ProvidesTicketOrderInformation;
use PHPUGDD\PHPDD\Website\Tickets\Application\Types\AttendeeName;
use PHPUGDD\PHPDD\Website\Tickets\Application\Types\CountryCode;
use PHPUGDD\PHPDD\Website\Tickets\Application\Types\DiversityDonation;
use PHPUGDD\PHPDD\Website\Tickets\Application\Types\PaymentFee;
use PHPUGDD\PHPDD\Website\Tickets\Application\Types\PaymentProvider;
use PHPUGDD\PHPDD\Website\Tickets\Application\Types\TicketOrderDate;
use PHPUGDD\PHPDD\Website\Tickets\Application\Types\TicketOrderDiscountTotal;
use PHPUGDD\PHPDD\Website\Tickets\Application\Types\TicketOrderEmailAddress;
use PHPUGDD\PHPDD\Website\Tickets\Application\Types\TicketOrderId;
use PHPUGDD\PHPDD\Website\Tickets\Application\Types\TicketOrderPaymentTotal;
use PHPUGDD\PHPDD\Website\Tickets\Application\Types\TicketOrderTotal;
use PHPUGDD\PHPDD\Website\Tickets\Application\Types\TicketType;
use PHPUGDD\PHPDD\Website\Tickets\Traits\MoneyProviding;
use function in_array;

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

	/** @var CalculatesPaymentFee */
	private $paymentFeeCalculator;

	/** @var null|PaymentProvider */
	private $paymentProvider;

	/** @var null|TicketOrderPayment */
	private $payment;

	/**
	 * @param TicketOrderId        $orderId
	 * @param TicketOrderDate      $orderDate
	 * @param CalculatesPaymentFee $paymentFeeCalculator
	 */
	public function __construct(
		TicketOrderId $orderId,
		TicketOrderDate $orderDate,
		CalculatesPaymentFee $paymentFeeCalculator
	)
	{
		$this->orderId              = $orderId;
		$this->orderDate            = $orderDate;
		$this->paymentFeeCalculator = $paymentFeeCalculator;
		$this->ticketItems          = new TicketItemCollection();
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

		$attendeeName = $ticketItem->getAttendeeName();

		if ( $this->attendeeHasConflictingWorkshops( $attendeeName, $ticketType ) )
		{
			throw new AllowedTicketCountPerAttendeeExceededException(
				sprintf(
					'%s cannot attend conflicting workshops.',
					$attendeeName->toString()
				)
			);
		}

		if ( $this->attendeeHasConferenceTicket( $attendeeName, $ticketType ) )
		{
			throw new AllowedTicketCountPerAttendeeExceededException(
				sprintf(
					'%s cannot attend the conference twice at the same time.',
					$attendeeName->toString()
				)
			);
		}

		$this->ticketItems->add( $ticketItem );
	}

	private function getMaxCountForTicketType( TicketType $ticketType ) : int
	{
		$maxCount = self::CONFERENCE_TICKETS_MAX;

		if ( in_array( $ticketType->toString(), TicketTypes::HALFDAY_WORKSHOPS, true ) )
		{
			$maxCount = self::WORKSHOP_TICKETS_MAX;
		}

		return $maxCount;
	}

	private function attendeeHasConflictingWorkshops( AttendeeName $attendeeName, TicketType $ticketType ) : bool
	{
		if ( $this->attendeeHasWorkshopTicketOfSameType( $attendeeName, $ticketType ) )
		{
			return true;
		}

		$fulldayWorkshop       = new TicketType( TicketTypes::FULLDAY_WORKSHOP );
		$countFulldayWorkshops = $this->ticketItems->getCountForTypeAndAttendeeName( $fulldayWorkshop, $attendeeName );
		$hasFulldayWorkshop    = ($countFulldayWorkshops > 0);

		if ( $hasFulldayWorkshop && in_array( $ticketType->toString(), TicketTypes::HALFDAY_WORKSHOPS, true ) )
		{
			return true;
		}

		$countHalfDayWorkshops = 0;
		foreach ( TicketTypes::HALFDAY_WORKSHOPS as $halfdayWorkshopType )
		{
			$halfdayWorkshop       = new TicketType( $halfdayWorkshopType );
			$countFulldayWorkshops += $this->ticketItems->getCountForTypeAndAttendeeName(
				$halfdayWorkshop,
				$attendeeName
			);
		}

		return $countFulldayWorkshops > 0 && $ticketType->equals( $fulldayWorkshop );
	}

	private function attendeeHasWorkshopTicketOfSameType( AttendeeName $attendeeName, TicketType $ticketType ) : bool
	{
		if ( !in_array( $ticketType->toString(), TicketTypes::ALL_WORKSHOPS, true ) )
		{
			return false;
		}

		return $this->ticketItems->getCountForTypeAndAttendeeName( $ticketType, $attendeeName ) > 0;
	}

	private function attendeeHasConferenceTicket( AttendeeName $attendeeName, TicketType $ticketType ) : bool
	{
		if ( TicketTypes::CONFERENCE !== $ticketType->toString() )
		{
			return false;
		}

		return $this->ticketItems->getCountForTypeAndAttendeeName( $ticketType, $attendeeName ) > 0;
	}

	public function getOrderId() : TicketOrderId
	{
		return $this->orderId;
	}

	public function getOrderDate() : TicketOrderDate
	{
		return $this->orderDate;
	}

	/**
	 * @return CollectsTicketItems|TicketItem[]
	 */
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
	 * @throws \InvalidArgumentException
	 * @throws InvalidArgumentException
	 * @return PaymentFee
	 */
	public function getPaymentFee() : PaymentFee
	{
		$orderTotal    = $this->getOrderTotal();
		$discountTotal = $this->getDiscountTotal();
		$money         = $orderTotal->getMoney()->add( $discountTotal->getMoney() );

		if ( null !== $this->diversityDonation )
		{
			$money = $money->add( $this->diversityDonation->getMoney() );
		}

		$countryCode = new CountryCode( CountryCodes::DE_SHORT );
		if ( null !== $this->billingAddress )
		{
			$countryCode = $this->billingAddress->getCountryCode();
		}

		$paymentFee = $this->paymentFeeCalculator->getPaymentFee( $money, $countryCode );

		return new PaymentFee( $paymentFee );
	}

	/**
	 * @throws InvalidArgumentException
	 * @return TicketOrderPaymentTotal
	 * @throws \InvalidArgumentException
	 */
	public function getPaymentTotal() : TicketOrderPaymentTotal
	{
		$orderTotal    = $this->getOrderTotal();
		$discountTotal = $this->getDiscountTotal();
		$money         = $orderTotal->getMoney()->subtract( $discountTotal->getMoney()->absolute() );

		if ( null !== $this->diversityDonation )
		{
			$money = $money->add( $this->diversityDonation->getMoney() );
		}

		$money = $money->add( $this->getPaymentFee()->getMoney() );

		return new TicketOrderPaymentTotal( $money );
	}

	public function payWith( PaymentProvider $paymentProvider ) : void
	{
		$this->paymentProvider = $paymentProvider;
	}

	public function getPaymentProvider() : ?PaymentProvider
	{
		return $this->paymentProvider;
	}

	public function assignPayment( TicketOrderPayment $ticketOrderPayment ) : void
	{
		$this->payment = $ticketOrderPayment;
	}

	public function getPayment() : ?TicketOrderPayment
	{
		return $this->payment;
	}
}
