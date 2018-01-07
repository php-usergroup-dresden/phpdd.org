<?php declare(strict_types=1);
/**
 * @author hollodotme
 */

namespace PHPUGDD\PHPDD\Website\Tests\Unit\Application\Tickets;

use PHPUGDD\PHPDD\Website\Application\Constants\TicketTypes;
use PHPUGDD\PHPDD\Website\Application\Tickets\Exceptions\AllowedTicketCountExceededException;
use PHPUGDD\PHPDD\Website\Application\Tickets\Exceptions\AllowedTicketCountPerAttendeeExceededException;
use PHPUGDD\PHPDD\Website\Application\Tickets\TicketItem;
use PHPUGDD\PHPDD\Website\Application\Tickets\TicketOrder;
use PHPUGDD\PHPDD\Website\Application\Types\AttendeeName;
use PHPUGDD\PHPDD\Website\Application\Types\TicketOrderDate;
use PHPUGDD\PHPDD\Website\Application\Types\TicketOrderId;
use PHPUGDD\PHPDD\Website\Tests\Fixtures\Traits\DiscountItemProviding;
use PHPUGDD\PHPDD\Website\Tests\Fixtures\Traits\TicketProviding;
use PHPUGDD\PHPDD\Website\Traits\MoneyProviding;
use PHPUnit\Framework\TestCase;

/**
 * Class TicketOrderTest
 * @package PHPUGDD\PHPDD\Website\Tests\Unit\Application\Tickets
 */
final class TicketOrderTest extends TestCase
{
	use TicketProviding;
	use MoneyProviding;
	use DiscountItemProviding;

	/**
	 * @throws \PHPUnit\Framework\Exception
	 * @throws \InvalidArgumentException
	 */
	public function testCanCreateInstanceFromOrderIdAndDate() : void
	{
		/** @var TicketOrderId $orderId */
		$orderId                = TicketOrderId::generate();
		$orderDate              = new TicketOrderDate( '2018-01-06 17:48:17' );
		$ticketOrder            = new TicketOrder( $orderId, $orderDate );
		$expectedBillingAddress = "\n \n\n\nDE- ";

		$this->assertInstanceOf( TicketOrder::class, $ticketOrder );

		$this->assertSame( '0', $ticketOrder->getDiscountTotal()->getMoney()->getAmount() );
		$this->assertSame( '0', $ticketOrder->getOrderTotal()->getMoney()->getAmount() );
		$this->assertSame( '0', $ticketOrder->getPaymentTotal()->getMoney()->getAmount() );
		$this->assertSame( '0', $ticketOrder->getDiversityDonation()->getMoney()->getAmount() );

		$this->assertCount( 0, $ticketOrder->getTicketItems() );
		$this->assertCount( 0, $ticketOrder->getDiscountItems() );

		$this->assertTrue( $orderId->equals( $ticketOrder->getOrderId() ) );
		$this->assertSame( $orderDate, $ticketOrder->getOrderDate() );

		$this->assertSame( 'you@example.com', $ticketOrder->getEmailAddress()->toString() );

		$this->assertSame( $expectedBillingAddress, $ticketOrder->getBillingAddress()->toString() );
	}

	/**
	 * @throws \PHPUnit\Framework\Exception
	 * @throws AllowedTicketCountExceededException
	 * @throws AllowedTicketCountPerAttendeeExceededException
	 * @throws \InvalidArgumentException
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
	 * @throws \PHPUnit\Framework\Exception
	 * @throws AllowedTicketCountExceededException
	 * @throws AllowedTicketCountPerAttendeeExceededException
	 * @throws \InvalidArgumentException
	 */
	public function testThrowsExceptionForExceedingMaxWorkshopTicketCount() : void
	{
		/** @var TicketOrderId $orderId */
		$orderId     = TicketOrderId::generate();
		$ticketOrder = new TicketOrder( $orderId, new TicketOrderDate() );
		$ticketItems = [];

		for ( $i = 0; $i < 11; $i++ )
		{
			$ticket = $this->getWorkshopTicket( TicketTypes::WORKSHOP_SLOT_A, 'Workshop Ticket', 'Workshop description', $this->getMoney( 25000 ) );

			$ticketItems[] = new TicketItem( $ticket, new AttendeeName( 'John Doe ' . $i ) );
		}

		$this->expectException( AllowedTicketCountExceededException::class );

		$ticketOrder->orderTickets( ...$ticketItems );
	}

	/**
	 * @throws AllowedTicketCountExceededException
	 * @throws \PHPUnit\Framework\Exception
	 * @throws AllowedTicketCountPerAttendeeExceededException
	 * @throws \InvalidArgumentException
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
	 * @throws AllowedTicketCountExceededException
	 * @throws \PHPUnit\Framework\Exception
	 * @throws AllowedTicketCountPerAttendeeExceededException
	 * @throws \InvalidArgumentException
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
	 * @throws AllowedTicketCountExceededException
	 * @throws AllowedTicketCountPerAttendeeExceededException
	 * @throws \PHPUGDD\PHPDD\Website\Application\Tickets\Exceptions\DiscountExceededTicketPriceException
	 * @throws \InvalidArgumentException
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

		$workshopDiscount   = $this->getDiscountItem( 'Workshop discount', '9D8C7B6A', 'Reduces ticket price', $this->getMoney( -3500 ) );
		$conferenceDiscount = $this->getDiscountItem( 'Conference discount', 'A2B3C4D5', 'Reduces ticket price', $this->getMoney( -2000 ) );

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
}
