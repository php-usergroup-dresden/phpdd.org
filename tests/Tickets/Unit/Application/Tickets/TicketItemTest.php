<?php declare(strict_types=1);

namespace PHPUGDD\PHPDD\Website\Tests\Tickets\Unit\Application\Tickets;

use PHPUGDD\PHPDD\Website\Tests\Tickets\Fixtures\Traits\DiscountItemProviding;
use PHPUGDD\PHPDD\Website\Tests\Tickets\Fixtures\Traits\TicketProviding;
use PHPUGDD\PHPDD\Website\Tickets\Application\Tickets\DiscountItem;
use PHPUGDD\PHPDD\Website\Tickets\Application\Tickets\Exceptions\DiscountExceededTicketPriceException;
use PHPUGDD\PHPDD\Website\Tickets\Application\Tickets\TicketItem;
use PHPUGDD\PHPDD\Website\Tickets\Application\Types\AttendeeName;
use PHPUGDD\PHPDD\Website\Tickets\Traits\MoneyProviding;
use PHPUnit\Framework\TestCase;

final class TicketItemTest extends TestCase
{
	use TicketProviding;
	use MoneyProviding;
	use DiscountItemProviding;

	/**
	 * @throws \PHPUnit\Framework\Exception
	 * @throws \InvalidArgumentException
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
	 * @throws \PHPUGDD\PHPDD\Website\Tickets\Application\Tickets\Exceptions\DiscountExceededTicketPriceException
	 * @throws \InvalidArgumentException
	 * @throws \PHPUnit\Framework\ExpectationFailedException
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
	 * @throws \InvalidArgumentException
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
