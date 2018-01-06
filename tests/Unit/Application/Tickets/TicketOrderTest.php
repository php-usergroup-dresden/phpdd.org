<?php declare(strict_types=1);
/**
 * @author hollodotme
 */

namespace PHPUGDD\PHPDD\Website\Tests\Unit\Application\Tickets;

use Money\Currency;
use Money\Money;
use PHPUGDD\PHPDD\Website\Application\Constants\TicketTypes;
use PHPUGDD\PHPDD\Website\Application\Tickets\Exceptions\AllowedTicketCountExceeded;
use PHPUGDD\PHPDD\Website\Application\Tickets\Ticket;
use PHPUGDD\PHPDD\Website\Application\Tickets\TicketItem;
use PHPUGDD\PHPDD\Website\Application\Tickets\TicketOrder;
use PHPUGDD\PHPDD\Website\Application\Types\AttendeeName;
use PHPUGDD\PHPDD\Website\Application\Types\TicketDescription;
use PHPUGDD\PHPDD\Website\Application\Types\TicketImage;
use PHPUGDD\PHPDD\Website\Application\Types\TicketName;
use PHPUGDD\PHPDD\Website\Application\Types\TicketOrderDate;
use PHPUGDD\PHPDD\Website\Application\Types\TicketOrderId;
use PHPUGDD\PHPDD\Website\Application\Types\TicketPrice;
use PHPUGDD\PHPDD\Website\Application\Types\TicketType;
use PHPUnit\Framework\TestCase;

/**
 * Class TicketOrderTest
 * @package PHPUGDD\PHPDD\Website\Tests\Unit\Application\Tickets
 */
final class TicketOrderTest extends TestCase
{
	/**
	 * @throws \PHPUnit\Framework\Exception
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
	 * @throws AllowedTicketCountExceeded
	 */
	public function testThrowsExceptionForExceedingMaxConferenceTicketCount() : void
	{
		/** @var TicketOrderId $orderId */
		$orderId     = TicketOrderId::generate();
		$ticketOrder = new TicketOrder( $orderId, new TicketOrderDate() );
		$ticketItems = [];

		for ( $i = 0; $i < 12; $i++ )
		{
			$ticket = new Ticket(
				new TicketType( TicketTypes::CONFERENCE ),
				new TicketName( 'Conference ticket' ),
				new TicketDescription( 'Grant access to the conference day of PHPDD.' ),
				new TicketPrice( new Money( 8900, new Currency( 'EUR' ) ) )
			);

			$ticketItems[] = new TicketItem( $ticket, new AttendeeName( 'John Doe ' . $i ) );
		}

		$this->expectException( AllowedTicketCountExceeded::class );

		$ticketOrder->orderTickets( ...$ticketItems );
	}

	/**
	 * @throws \PHPUnit\Framework\Exception
	 * @throws AllowedTicketCountExceeded
	 */
	public function testThrowsExceptionForExceedingMaxWorkshopTicketCount() : void
	{
		/** @var TicketOrderId $orderId */
		$orderId     = TicketOrderId::generate();
		$ticketOrder = new TicketOrder( $orderId, new TicketOrderDate() );
		$ticketItems = [];

		for ( $i = 0; $i < 5; $i++ )
		{
			$ticket = new Ticket(
				new TicketType( TicketTypes::WORKSHOP ),
				new TicketName( 'Conference ticket' ),
				new TicketDescription( 'Grant access to the conference day of PHPDD.' ),
				new TicketPrice( new Money( 8900, new Currency( 'EUR' ) ) )
			);

			$ticketItems[] = new TicketItem( $ticket, new AttendeeName( 'John Doe ' . $i ) );
		}

		$this->expectException( AllowedTicketCountExceeded::class );

		$ticketOrder->orderTickets( ...$ticketItems );
	}
}
