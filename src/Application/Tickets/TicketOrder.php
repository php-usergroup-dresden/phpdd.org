<?php declare(strict_types=1);
/**
 * @author hollodotme
 */

namespace PHPUGDD\PHPDD\Website\Application\Tickets;

use Money\Currency;
use Money\Money;
use PHPUGDD\PHPDD\Website\Application\Constants\CountryCodes;
use PHPUGDD\PHPDD\Website\Application\Constants\TicketTypes;
use PHPUGDD\PHPDD\Website\Application\Tickets\Exceptions\AllowedTicketCountExceeded;
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

/**
 * Class TicketOrder
 * @package PHPUGDD\PHPDD\Website\Application\Tickets
 */
final class TicketOrder implements ProvidesTicketOrderInformation
{
	private const WORKSHOP_TICKETS_MAX   = 3;

	private const CONFERENCE_TICKETS_MAX = 10;

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
		$this->diversityDonation = new DiversityDonation( new Money( 0, new Currency( 'EUR' ) ) );
	}

	/**
	 * @param TicketItem[] ...$ticketItems
	 *
	 * @throws AllowedTicketCountExceeded
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
	 * @throws AllowedTicketCountExceeded
	 */
	private function orderTicket( TicketItem $ticketItem ) : void
	{
		$ticketType  = $ticketItem->getTicket()->getType();
		$ticketCount = $this->ticketItems->getCountForType( $ticketType );
		$maxCount    = $this->getMaxCountForTicketType( $ticketType );

		if ( $ticketCount >= $maxCount )
		{
			throw new AllowedTicketCountExceeded( sprintf( 'Allowed ticket count of %d exceeded.', $maxCount ) );
		}

		$this->ticketItems->add( $ticketItem );
	}

	private function getMaxCountForTicketType( TicketType $ticketType )
	{
		$maxCount = self::CONFERENCE_TICKETS_MAX;

		if ( TicketTypes::WORKSHOP === $ticketType->toString() )
		{
			$maxCount = self::WORKSHOP_TICKETS_MAX;
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

	public function getOrderTotal() : TicketOrderTotal
	{
		$money = new Money( 0, new Currency( 'EUR' ) );

		foreach ( $this->ticketItems as $ticketItem )
		{
			$money = $money->add( $ticketItem->getTicket()->getPrice()->getMoney() );
		}

		return new TicketOrderTotal( $money );
	}

	public function getDiscountTotal() : TicketOrderDiscountTotal
	{
		$money = new Money( 0, new Currency( 'EUR' ) );

		foreach ( $this->ticketItems as $ticketItem )
		{
			$money = $money->add( $ticketItem->getDiscountItem()->getDiscountPrice()->getMoney() );
		}

		return new TicketOrderDiscountTotal( $money );
	}

	public function getPaymentTotal() : TicketOrderPaymentTotal
	{
		$orderTotal    = $this->getOrderTotal();
		$discountTotal = $this->getDiscountTotal();
		$money         = $orderTotal->getMoney()->subtract( $discountTotal->getMoney() );

		return new TicketOrderPaymentTotal( $money );
	}
}
