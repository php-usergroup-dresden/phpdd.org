<?php declare(strict_types=1);

namespace PHPUGDD\PHPDD\Website\Tests\Tickets\Unit\Application\Tickets;

use PHPUGDD\PHPDD\Website\Tests\Tickets\Fixtures\Traits\DiscountItemProviding;
use PHPUGDD\PHPDD\Website\Tests\Tickets\Fixtures\Traits\TicketProviding;
use PHPUGDD\PHPDD\Website\Tickets\Application\Constants\CountryCodes;
use PHPUGDD\PHPDD\Website\Tickets\Application\Constants\TicketTypes;
use PHPUGDD\PHPDD\Website\Tickets\Application\Tickets\Exceptions\AllowedTicketCountExceededException;
use PHPUGDD\PHPDD\Website\Tickets\Application\Tickets\Exceptions\AllowedTicketCountPerAttendeeExceededException;
use PHPUGDD\PHPDD\Website\Tickets\Application\Tickets\TicketItem;
use PHPUGDD\PHPDD\Website\Tickets\Application\Tickets\TicketOrder;
use PHPUGDD\PHPDD\Website\Tickets\Application\Tickets\TicketOrderBillingAddress;
use PHPUGDD\PHPDD\Website\Tickets\Application\Types\AddressAddon;
use PHPUGDD\PHPDD\Website\Tickets\Application\Types\AttendeeName;
use PHPUGDD\PHPDD\Website\Tickets\Application\Types\City;
use PHPUGDD\PHPDD\Website\Tickets\Application\Types\CompanyName;
use PHPUGDD\PHPDD\Website\Tickets\Application\Types\CountryCode;
use PHPUGDD\PHPDD\Website\Tickets\Application\Types\Firstname;
use PHPUGDD\PHPDD\Website\Tickets\Application\Types\Lastname;
use PHPUGDD\PHPDD\Website\Tickets\Application\Types\StreetWithNumber;
use PHPUGDD\PHPDD\Website\Tickets\Application\Types\TicketOrderDate;
use PHPUGDD\PHPDD\Website\Tickets\Application\Types\TicketOrderEmailAddress;
use PHPUGDD\PHPDD\Website\Tickets\Application\Types\TicketOrderId;
use PHPUGDD\PHPDD\Website\Tickets\Application\Types\VatNumber;
use PHPUGDD\PHPDD\Website\Tickets\Application\Types\ZipCode;
use PHPUGDD\PHPDD\Website\Tickets\Traits\MoneyProviding;
use PHPUnit\Framework\TestCase;

final class TicketOrderTest extends TestCase
{
	use TicketProviding;
	use MoneyProviding;
	use DiscountItemProviding;

	/**
	 * @throws \PHPUnit\Framework\Exception
	 * @throws \InvalidArgumentException
	 * @throws \Exception
	 */
	public function testCanCreateInstanceFromOrderIdAndDate() : void
	{
		/** @var TicketOrderId $orderId */
		$orderId     = TicketOrderId::generate();
		$orderDate   = new TicketOrderDate( '2018-01-06 17:48:17' );
		$ticketOrder = new TicketOrder( $orderId, $orderDate );

		$this->assertInstanceOf( TicketOrder::class, $ticketOrder );

		$this->assertSame( '0', $ticketOrder->getDiscountTotal()->getMoney()->getAmount() );
		$this->assertSame( '0', $ticketOrder->getOrderTotal()->getMoney()->getAmount() );
		$this->assertSame( '0', $ticketOrder->getPaymentTotal()->getMoney()->getAmount() );
		$this->assertNull( $ticketOrder->getDiversityDonation() );

		$this->assertCount( 0, $ticketOrder->getTicketItems() );
		$this->assertCount( 0, $ticketOrder->getDiscountItems() );

		$this->assertTrue( $orderId->equals( $ticketOrder->getOrderId() ) );
		$this->assertSame( $orderDate, $ticketOrder->getOrderDate() );

		$this->assertFalse( $ticketOrder->isPlaceable() );
	}

	/**
	 * @throws \InvalidArgumentException
	 * @throws \PHPUnit\Framework\ExpectationFailedException
	 * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
	 * @throws \Exception
	 */
	public function testTicketOrderGetsPlaceableIfEmailBillingAddressAndTicketsWereSet() : void
	{
		/** @var TicketOrderId $orderId */
		$orderId     = TicketOrderId::generate();
		$orderDate   = new TicketOrderDate( '2018-01-06 17:48:17' );
		$ticketOrder = new TicketOrder( $orderId, $orderDate );

		$email = new TicketOrderEmailAddress( 'test@example.com' );

		$ticketOrder->sendTicketsAndInvoiceTo( $email );

		$this->assertSame( $email, $ticketOrder->getEmailAddress() );
		$this->assertFalse( $ticketOrder->isPlaceable() );

		$billingAddress = new TicketOrderBillingAddress(
			new Firstname( 'Unit' ),
			new Lastname( 'Tester' ),
			new CompanyName( 'Unit Test Company' ),
			new StreetWithNumber( 'Unit-Test-Str. 123b' ),
			new AddressAddon( 'Hand to Testers' ),
			new ZipCode( '01234' ),
			new City( 'Testcity' ),
			new CountryCode( CountryCodes::DE_SHORT ),
			new VatNumber( 'DE 123 456 789' )
		);

		$ticketOrder->billTo( $billingAddress );

		$this->assertSame( $billingAddress, $ticketOrder->getBillingAddress() );
		$this->assertFalse( $ticketOrder->isPlaceable() );

		$ticket     = $this->getConferenceTicket( $this->getMoney( 9900 ) );
		$ticketItem = new TicketItem( $ticket, new AttendeeName( 'John Doe' ) );

		$ticketOrder->orderTickets( $ticketItem );

		$this->assertCount( 1, $ticketOrder->getTicketItems() );
		$this->assertTrue( $ticketOrder->isPlaceable() );
	}

	/**
	 * @throws \Fortuneglobe\Types\Exceptions\InvalidArgumentException
	 * @throws \InvalidArgumentException
	 * @throws AllowedTicketCountExceededException
	 * @throws AllowedTicketCountPerAttendeeExceededException
	 * @throws \Exception
	 */
	public function testThrowsExceptionForExceedingMaxConferenceTicketCount() : void
	{
		/** @var TicketOrderId $orderId */
		$orderId     = TicketOrderId::generate();
		$ticketOrder = new TicketOrder( $orderId, new TicketOrderDate() );
		$ticketItems = [];

		for ( $i = 0; $i < 11; $i++ )
		{
			$ticket = $this->getConferenceTicket( $this->getMoney( 8900 ) );

			$ticketItems[] = new TicketItem( $ticket, new AttendeeName( 'John Doe ' . $i ) );
		}

		$this->expectException( AllowedTicketCountExceededException::class );

		$ticketOrder->orderTickets( ...$ticketItems );
	}

	/**
	 * @throws \Fortuneglobe\Types\Exceptions\InvalidArgumentException
	 * @throws \InvalidArgumentException
	 * @throws AllowedTicketCountExceededException
	 * @throws AllowedTicketCountPerAttendeeExceededException
	 * @throws \Exception
	 */
	public function testThrowsExceptionForExceedingMaxWorkshopTicketCount() : void
	{
		/** @var TicketOrderId $orderId */
		$orderId     = TicketOrderId::generate();
		$ticketOrder = new TicketOrder( $orderId, new TicketOrderDate() );
		$ticketItems = [];

		for ( $i = 0; $i < 11; $i++ )
		{
			$ticket = $this->getWorkshopTicket(
				TicketTypes::WORKSHOP_SLOT_A,
				'Workshop Ticket',
				'Workshop description',
				$this->getMoney( 25000 )
			);

			$ticketItems[] = new TicketItem( $ticket, new AttendeeName( 'John Doe ' . $i ) );
		}

		$this->expectException( AllowedTicketCountExceededException::class );

		$ticketOrder->orderTickets( ...$ticketItems );
	}

	/**
	 * @throws \Fortuneglobe\Types\Exceptions\InvalidArgumentException
	 * @throws \InvalidArgumentException
	 * @throws AllowedTicketCountExceededException
	 * @throws AllowedTicketCountPerAttendeeExceededException
	 * @throws \Exception
	 */
	public function testThrowsExceptionForExceedingMaxWorkshopTicketCountPerAttendee() : void
	{
		/** @var TicketOrderId $orderId */
		$orderId      = TicketOrderId::generate();
		$ticketOrder  = new TicketOrder( $orderId, new TicketOrderDate() );
		$ticketItems  = [];
		$attendeeName = new AttendeeName( 'John Doe' );

		for ( $i = 0; $i < 2; $i++ )
		{
			$ticket = $this->getWorkshopTicket(
				TicketTypes::WORKSHOP_SLOT_A,
				'Workshop Ticket',
				'Workshop description',
				$this->getMoney( 25000 )
			);

			$ticketItems[] = new TicketItem( $ticket, $attendeeName );
		}

		$this->expectException( AllowedTicketCountPerAttendeeExceededException::class );

		$ticketOrder->orderTickets( ...$ticketItems );
	}

	/**
	 * @throws \Fortuneglobe\Types\Exceptions\InvalidArgumentException
	 * @throws \InvalidArgumentException
	 * @throws AllowedTicketCountExceededException
	 * @throws AllowedTicketCountPerAttendeeExceededException
	 * @throws \Exception
	 */
	public function testThrowsExceptionForExceedingMaxConferenceTicketCountPerAttendee() : void
	{
		/** @var TicketOrderId $orderId */
		$orderId      = TicketOrderId::generate();
		$ticketOrder  = new TicketOrder( $orderId, new TicketOrderDate() );
		$ticketItems  = [];
		$attendeeName = new AttendeeName( 'John Doe' );

		for ( $i = 0; $i < 2; $i++ )
		{
			$ticket = $this->getConferenceTicket( $this->getMoney( 25000 ) );

			$ticketItems[] = new TicketItem( $ticket, $attendeeName );
		}

		$this->expectException( AllowedTicketCountPerAttendeeExceededException::class );

		$ticketOrder->orderTickets( ...$ticketItems );
	}

	/**
	 * @throws AllowedTicketCountExceededException
	 * @throws AllowedTicketCountPerAttendeeExceededException
	 * @throws \PHPUnit\Framework\Exception
	 * @throws \InvalidArgumentException
	 * @throws \Exception
	 */
	public function testSameAttendeeCanOrderAWorkshopTicketForEachSlot() : void
	{
		/** @var TicketOrderId $orderId */
		$orderId     = TicketOrderId::generate();
		$ticketOrder = new TicketOrder( $orderId, new TicketOrderDate() );
		$johnDoe     = new AttendeeName( 'John Doe' );
		$janeDoe     = new AttendeeName( 'Jane Doe' );

		$ticketSlotA = $this->getWorkshopTicket(
			TicketTypes::WORKSHOP_SLOT_A,
			'Workshop Ticket Slot A',
			'Workshop description slot A',
			$this->getMoney( 25000 )
		);

		$ticketSlotB = $this->getWorkshopTicket(
			TicketTypes::WORKSHOP_SLOT_B,
			'Workshop Ticket Slot B',
			'Workshop description slot B',
			$this->getMoney( 25000 )
		);

		# John Doe can order a workshop ticket for slot A
		$ticketOrder->orderTickets( new TicketItem( $ticketSlotA, $johnDoe ) );

		$this->assertCount( 1, $ticketOrder->getTicketItems() );

		# John Doe can order a workshop ticket for slot B
		$ticketOrder->orderTickets( new TicketItem( $ticketSlotB, $johnDoe ) );

		$this->assertCount( 2, $ticketOrder->getTicketItems() );

		# John Doe cannot order another workshop ticket for slot A
		try
		{
			$ticketOrder->orderTickets( new TicketItem( $ticketSlotA, $johnDoe ) );
		}
		catch ( \Throwable $e )
		{
			$this->assertInstanceOf( AllowedTicketCountPerAttendeeExceededException::class, $e );
		}

		# Jane Doe can order a workshop ticket for slot A
		$ticketOrder->orderTickets( new TicketItem( $ticketSlotA, $janeDoe ) );

		$this->assertCount( 3, $ticketOrder->getTicketItems() );

		# Jane Doe can order a workshop ticket for slot B
		$ticketOrder->orderTickets( new TicketItem( $ticketSlotB, $janeDoe ) );

		$this->assertCount( 4, $ticketOrder->getTicketItems() );

		# Jane Doe cannot order another workshop ticket for slot A
		try
		{
			$ticketOrder->orderTickets( new TicketItem( $ticketSlotB, $janeDoe ) );
		}
		catch ( \Throwable $e )
		{
			$this->assertInstanceOf( AllowedTicketCountPerAttendeeExceededException::class, $e );
		}
	}

	/**
	 * @throws \Fortuneglobe\Types\Exceptions\InvalidArgumentException
	 * @throws \InvalidArgumentException
	 * @throws AllowedTicketCountExceededException
	 * @throws AllowedTicketCountPerAttendeeExceededException
	 * @throws \PHPUGDD\PHPDD\Website\Tickets\Application\Tickets\Exceptions\DiscountExceededTicketPriceException
	 * @throws \PHPUnit\Framework\ExpectationFailedException
	 * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
	 * @throws \Exception
	 */
	public function testCanGetTotals() : void
	{
		/** @var TicketOrderId $orderId */
		$orderId     = TicketOrderId::generate();
		$ticketOrder = new TicketOrder( $orderId, new TicketOrderDate() );
		$johnDoe     = new AttendeeName( 'John Doe' );

		$ticketSlotA = $this->getWorkshopTicket(
			TicketTypes::WORKSHOP_SLOT_A,
			'Workshop Ticket Slot A',
			'Workshop description slot A',
			$this->getMoney( 25000 )
		);

		$ticketSlotB = $this->getWorkshopTicket(
			TicketTypes::WORKSHOP_SLOT_B,
			'Workshop Ticket Slot B',
			'Workshop description slot B',
			$this->getMoney( 25000 )
		);

		$conferenceTicket = $this->getConferenceTicket( $this->getMoney( 8900 ) );

		$workshopDiscount   = $this->getDiscountItem(
			'Workshop discount',
			'D87318324E',
			'Reduces ticket price',
			$this->getMoney( -3500 ),
			['Workshop Ticket Slot B']
		);
		$conferenceDiscount = $this->getDiscountItem(
			'Conference discount',
			'P95318357E',
			'Reduces ticket price',
			$this->getMoney( -2000 ),
			['Conference Ticket']
		);

		$ticketItemSlotA      = new TicketItem( $ticketSlotA, $johnDoe );
		$ticketItemSlotB      = new TicketItem( $ticketSlotB, $johnDoe );
		$ticketItemConference = new TicketItem( $conferenceTicket, $johnDoe );

		$ticketItemSlotB->grantDiscount( $workshopDiscount );
		$ticketItemConference->grantDiscount( $conferenceDiscount );

		$ticketOrder->orderTickets( $ticketItemSlotA, $ticketItemSlotB, $ticketItemConference );

		$this->assertSame( '58900', $ticketOrder->getOrderTotal()->getMoney()->getAmount() );
		$this->assertSame( '-5500', $ticketOrder->getDiscountTotal()->getMoney()->getAmount() );
		$this->assertSame( '53400', $ticketOrder->getPaymentTotal()->getMoney()->getAmount() );
	}

	/**
	 * @throws AllowedTicketCountExceededException
	 * @throws AllowedTicketCountPerAttendeeExceededException
	 * @throws \Fortuneglobe\Types\Exceptions\InvalidArgumentException
	 * @throws \InvalidArgumentException
	 * @throws \PHPUnit\Framework\ExpectationFailedException
	 * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
	 * @throws \Exception
	 */
	public function testTicketItemsWithoutDiscountItemDoNotAddDiscounts() : void
	{
		/** @var TicketOrderId $orderId */
		$orderId     = TicketOrderId::generate();
		$ticketOrder = new TicketOrder( $orderId, new TicketOrderDate() );
		$johnDoe     = new AttendeeName( 'John Doe' );

		$ticket = $this->getWorkshopTicket(
			TicketTypes::WORKSHOP_SLOT_A,
			'Workshop Ticket Slot A',
			'Workshop description slot A',
			$this->getMoney( 25000 )
		);

		$ticketItem = new TicketItem( $ticket, $johnDoe );

		$ticketOrder->orderTickets( $ticketItem );

		$this->assertCount( 0, $ticketOrder->getDiscountItems() );

		$expectedMoney = $this->getMoney( 0 );

		$this->assertTrue( $expectedMoney->equals( $ticketOrder->getDiscountTotal()->getMoney() ) );
	}
}
