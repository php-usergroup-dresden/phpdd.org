<?php declare(strict_types=1);
/**
 * @author hollodotme
 */

namespace PHPUGDD\PHPDD\Website\Tests\Unit\Application\Tickets;

use PHPUGDD\PHPDD\Website\Application\Tickets\DiscountItem;
use PHPUGDD\PHPDD\Website\Application\Tickets\Exceptions\DiscountExceededTicketPriceException;
use PHPUGDD\PHPDD\Website\Application\Tickets\TicketItem;
use PHPUGDD\PHPDD\Website\Application\Types\AttendeeName;
use PHPUGDD\PHPDD\Website\Tests\Fixtures\Traits\DiscountItemProviding;
use PHPUGDD\PHPDD\Website\Tests\Fixtures\Traits\MoneyProviding;
use PHPUGDD\PHPDD\Website\Tests\Fixtures\Traits\TicketProviding;
use PHPUnit\Framework\TestCase;

/**
 * Class TicketItemTest
 * @package PHPUGDD\PHPDD\Website\Tests\Unit\Application\Tickets
 */
final class TicketItemTest extends TestCase
{
	use TicketProviding;
	use MoneyProviding;
	use DiscountItemProviding;

	/**
	 * @throws \PHPUnit\Framework\Exception
	 */
	public function testCanCreateInstance() : void
	{
		$ticket     = $this->getConferenceTicket( $this->getMoney( 8900 ) );
		$ticketItem = new TicketItem( $ticket, new AttendeeName( 'John Doe' ) );

		$this->assertInstanceOf( TicketItem::class, $ticketItem );

		$this->assertSame( $ticket, $ticketItem->getTicket() );
		$this->assertSame( 'John Doe', $ticketItem->getAttendeeName()->toString() );
		$this->assertSame( '', $ticketItem->getDiscountItem()->getName()->toString() );
		$this->assertSame( '0OOOOOO0', $ticketItem->getDiscountItem()->getCode()->toString() );
		$this->assertSame( '', $ticketItem->getDiscountItem()->getDescription()->toString() );
		$this->assertSame( '0', $ticketItem->getDiscountItem()->getDiscountPrice()->getMoney()->getAmount() );
	}

	/**
	 * @param DiscountItem $discountItem
	 *
	 * @throws \PHPUGDD\PHPDD\Website\Application\Tickets\Exceptions\DiscountExceededTicketPriceException
	 *
	 * @dataProvider validDiscountItemProvider
	 */
	public function testCanGrantDiscounts( DiscountItem $discountItem ) : void
	{
		$ticket     = $this->getConferenceTicket( $this->getMoney( 8900 ) );
		$ticketItem = new TicketItem( $ticket, new AttendeeName( 'John Doe' ) );

		$ticketItem->grantDiscount( $discountItem );

		$this->assertSame( $discountItem, $ticketItem->getDiscountItem() );
	}

	/**
	 * @return array
	 * @throws \Exception
	 */
	public function validDiscountItemProvider() : array
	{
		return [
			[
				'discountItem' => $this->getDiscountItem( 'Member discount', '1AAAAAA1', 'Reduces price for members', $this->getMoney( -8900 ) ),
			],
			[
				'discountItem' => $this->getDiscountItem( 'Member discount', '1AAAAAA1', 'Reduces price for members', $this->getMoney( -7900 ) ),
			],
			[
				'discountItem' => $this->getDiscountItem( 'Member discount', '1AAAAAA1', 'Reduces price for members', $this->getMoney( 0 ) ),
			],
		];
	}

	/**
	 * @throws DiscountExceededTicketPriceException
	 * @throws \PHPUnit\Framework\Exception
	 */
	public function testThrowsExceptionIfDiscountExceedsTicketPrice() : void
	{
		$ticket     = $this->getConferenceTicket( $this->getMoney( 8900 ) );
		$ticketItem = new TicketItem( $ticket, new AttendeeName( 'John Doe' ) );

		$discountItem = $this->getDiscountItem( 'Member discount', '1AAAAAA1', 'Reduces price for members', $this->getMoney( -9000 ) );

		$this->expectException( DiscountExceededTicketPriceException::class );

		$ticketItem->grantDiscount( $discountItem );
	}
}
