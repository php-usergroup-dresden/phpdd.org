<?php declare(strict_types=1);

namespace PHPUGDD\PHPDD\Website\Tests\Tickets\Unit\Application\Tickets;

use Generator;
use PHPUGDD\PHPDD\Website\Tests\Tickets\Fixtures\Traits\DiscountItemProviding;
use PHPUGDD\PHPDD\Website\Tests\Tickets\Fixtures\Traits\TicketProviding;
use PHPUGDD\PHPDD\Website\Tickets\Application\Constants\CountryCodes;
use PHPUGDD\PHPDD\Website\Tickets\Application\Constants\TicketTypes;
use PHPUGDD\PHPDD\Website\Tickets\Application\Payments\Interfaces\CalculatesPaymentFee;
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
		$ticketOrder = new TicketOrder( $orderId, $orderDate, $this->getPaymentFeeCalculatorStub() );

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
	 * @return CalculatesPaymentFee
	 */
	private function getPaymentFeeCalculatorStub() : CalculatesPaymentFee
	{
		$stub = $this->getMockBuilder( CalculatesPaymentFee::class )->getMockForAbstractClass();
		$stub->method( 'getPaymentFee' )->willReturn( $this->getMoney( 0 ) );

		/** @var CalculatesPaymentFee $stub */
		return $stub;
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
		$ticketOrder = new TicketOrder( $orderId, $orderDate, $this->getPaymentFeeCalculatorStub() );

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
		$ticketOrder = new TicketOrder( $orderId, new TicketOrderDate(), $this->getPaymentFeeCalculatorStub() );
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
		$ticketOrder = new TicketOrder( $orderId, new TicketOrderDate(), $this->getPaymentFeeCalculatorStub() );
		$ticketItems = [];

		for ( $i = 0; $i < 11; $i++ )
		{
			$ticket = $this->getWorkshopTicket(
				TicketTypes::FULLDAY_WORKSHOP,
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
		$ticketOrder  = new TicketOrder( $orderId, new TicketOrderDate(), $this->getPaymentFeeCalculatorStub() );
		$ticketItems  = [];
		$attendeeName = new AttendeeName( 'John Doe' );

		for ( $i = 0; $i < 2; $i++ )
		{
			$ticket = $this->getWorkshopTicket(
				TicketTypes::FULLDAY_WORKSHOP,
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
		$ticketOrder  = new TicketOrder( $orderId, new TicketOrderDate(), $this->getPaymentFeeCalculatorStub() );
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
	 * @param array $workshopTickets
	 *
	 * @throws AllowedTicketCountExceededException
	 * @throws AllowedTicketCountPerAttendeeExceededException
	 * @throws \InvalidArgumentException
	 * @throws \Exception
	 * @dataProvider conflictingWorkshopTicketsProvider
	 */
	public function testSameAttendeeCannotOrderConflictingWorkshopTickets( array $workshopTickets ) : void
	{
		/** @var TicketOrderId $orderId */
		$orderId     = TicketOrderId::generate();
		$ticketOrder = new TicketOrder( $orderId, new TicketOrderDate(), $this->getPaymentFeeCalculatorStub() );
		$johnDoe     = new AttendeeName( 'John Doe' );

		$ticketItems = [];
		foreach ( $workshopTickets as $workshopTicket )
		{
			$ticketItems[] = new TicketItem( $workshopTicket, $johnDoe );
		}

		$this->expectException( AllowedTicketCountPerAttendeeExceededException::class );
		$this->expectExceptionMessage( 'John Doe cannot attend conflicting workshops.' );

		$ticketOrder->orderTickets( ...$ticketItems );
	}

	/**
	 * @throws \Fortuneglobe\Types\Exceptions\InvalidArgumentException
	 * @throws \InvalidArgumentException
	 * @return Generator
	 */
	public function conflictingWorkshopTicketsProvider() : Generator
	{
		# Two fullday workshops cannot be combined

		$fulldayWorkshopA = $this->getWorkshopTicket(
			TicketTypes::FULLDAY_WORKSHOP,
			'Full day workshop A',
			'Full day workshop description A',
			$this->getMoney( 24900 )
		);

		$fulldayWorkshopB = $this->getWorkshopTicket(
			TicketTypes::FULLDAY_WORKSHOP,
			'Full day workshop B',
			'Full day workshop description B',
			$this->getMoney( 24900 )
		);

		yield [
			'workshopTickets' => [$fulldayWorkshopA, $fulldayWorkshopB],
		];

		# Fullday & halfday workshops cannot be combined

		$fulldayWorkshop = $this->getWorkshopTicket(
			TicketTypes::FULLDAY_WORKSHOP,
			'Full day workshop',
			'Full day workshop description',
			$this->getMoney( 24900 )
		);

		$halfdayWorkshop = $this->getWorkshopTicket(
			TicketTypes::HALFDAY_WORKSHOP_A,
			'Half day workshop A',
			'Half day workshop description A',
			$this->getMoney( 14900 )
		);

		yield [
			'workshopTickets' => [$fulldayWorkshop, $halfdayWorkshop],
		];

		# And vice versa

		yield [
			'workshopTickets' => [clone $halfdayWorkshop, clone $fulldayWorkshop],
		];

		# To halfday workshops in the same time slot cannot be combined

		$halfdayWorkshopA1 = $this->getWorkshopTicket(
			TicketTypes::HALFDAY_WORKSHOP_A,
			'Half day workshop A1',
			'Half day workshop description A1',
			$this->getMoney( 14900 )
		);

		$halfdayWorkshopA2 = $this->getWorkshopTicket(
			TicketTypes::HALFDAY_WORKSHOP_A,
			'Half day workshop A2',
			'Half day workshop description A2',
			$this->getMoney( 14900 )
		);

		yield [
			'workshopTickets' => [$halfdayWorkshopA1, $halfdayWorkshopA2],
		];

		# And vice versa

		yield [
			'workshopTickets' => [$halfdayWorkshopA2, $halfdayWorkshopA1],
		];

		# To halfday workshops in the same time slot cannot be combined

		$halfdayWorkshopB1 = $this->getWorkshopTicket(
			TicketTypes::HALFDAY_WORKSHOP_B,
			'Half day workshop B1',
			'Half day workshop description B1',
			$this->getMoney( 14900 )
		);

		$halfdayWorkshopB2 = $this->getWorkshopTicket(
			TicketTypes::HALFDAY_WORKSHOP_B,
			'Half day workshop B2',
			'Half day workshop description B2',
			$this->getMoney( 14900 )
		);

		yield [
			'workshopTickets' => [$halfdayWorkshopB1, $halfdayWorkshopB2],
		];

		# And vice versa

		yield [
			'workshopTickets' => [$halfdayWorkshopB2, $halfdayWorkshopB1],
		];
	}

	/**
	 * @throws AllowedTicketCountExceededException
	 * @throws AllowedTicketCountPerAttendeeExceededException
	 * @throws \Fortuneglobe\Types\Exceptions\InvalidArgumentException
	 * @throws \InvalidArgumentException
	 * @throws \Exception
	 */
	public function testSameAttendeeCannotOrderMultipleConferenceTickets() : void
	{
		/** @var TicketOrderId $orderId */
		$orderId     = TicketOrderId::generate();
		$ticketOrder = new TicketOrder( $orderId, new TicketOrderDate(), $this->getPaymentFeeCalculatorStub() );
		$johnDoe     = new AttendeeName( 'John Doe' );

		$ticketItems = [
			new TicketItem( $this->getConferenceTicket( $this->getMoney( 119 ) ), $johnDoe ),
			new TicketItem( $this->getConferenceTicket( $this->getMoney( 119 ) ), $johnDoe ),
		];

		$this->expectException( AllowedTicketCountPerAttendeeExceededException::class );
		$this->expectExceptionMessage( 'John Doe cannot attend the conference twice at the same time.' );

		$ticketOrder->orderTickets( ...$ticketItems );
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
		$ticketOrder = new TicketOrder( $orderId, new TicketOrderDate(), $this->getPaymentFeeCalculatorStub() );
		$johnDoe     = new AttendeeName( 'John Doe' );

		$ticketSlotA = $this->getWorkshopTicket(
			TicketTypes::HALFDAY_WORKSHOP_A,
			'Workshop Ticket Slot A',
			'Workshop description slot A',
			$this->getMoney( 25000 )
		);

		$ticketSlotB = $this->getWorkshopTicket(
			TicketTypes::HALFDAY_WORKSHOP_B,
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
			['PHPDD18-WS-01']
		);
		$conferenceDiscount = $this->getDiscountItem(
			'Conference discount',
			'P95318357E',
			'Reduces ticket price',
			$this->getMoney( -2000 ),
			['PHPDD18-CT-01']
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
		$ticketOrder = new TicketOrder( $orderId, new TicketOrderDate(), $this->getPaymentFeeCalculatorStub() );
		$johnDoe     = new AttendeeName( 'John Doe' );

		$ticket = $this->getWorkshopTicket(
			TicketTypes::FULLDAY_WORKSHOP,
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
