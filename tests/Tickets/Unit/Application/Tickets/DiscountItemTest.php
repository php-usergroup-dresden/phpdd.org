<?php declare(strict_types=1);

namespace PHPUGDD\PHPDD\Website\Tests\Tickets\Unit\Application\Tickets;

use PHPUGDD\PHPDD\Website\Tickets\Application\Tickets\DiscountItem;
use PHPUGDD\PHPDD\Website\Tickets\Application\Types\DiscountCode;
use PHPUGDD\PHPDD\Website\Tickets\Application\Types\DiscountDescription;
use PHPUGDD\PHPDD\Website\Tickets\Application\Types\DiscountName;
use PHPUGDD\PHPDD\Website\Tickets\Application\Types\DiscountPrice;
use PHPUGDD\PHPDD\Website\Tickets\Application\Types\TicketId;
use PHPUGDD\PHPDD\Website\Tickets\Traits\MoneyProviding;
use PHPUnit\Framework\TestCase;

final class DiscountItemTest extends TestCase
{
	use MoneyProviding;

	/**
	 * @throws \Fortuneglobe\Types\Exceptions\InvalidArgumentException
	 * @throws \InvalidArgumentException
	 * @throws \PHPUnit\Framework\ExpectationFailedException
	 * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
	 */
	public function testCanConstructFromValues() : void
	{
		$discountName        = new DiscountName( 'Test discount' );
		$discountCode        = new DiscountCode( 'P95318357E' );
		$discountDescription = new DiscountDescription( 'Discount description' );
		$discountPrice       = new DiscountPrice( $this->getMoney( -3000 ) );
		$allowedTickets      = [];
		$ticketId            = new TicketId( 'PHPDD18-CT-01' );

		$discountItem = new DiscountItem(
			$discountName,
			$discountCode,
			$discountDescription,
			$discountPrice,
			$allowedTickets
		);

		$this->assertSame( $discountName, $discountItem->getName() );
		$this->assertSame( $discountCode, $discountItem->getCode() );
		$this->assertSame( $discountDescription, $discountItem->getDescription() );
		$this->assertSame( $discountPrice, $discountItem->getDiscountPrice() );
		$this->assertFalse( $discountItem->isAllowedForTicket( $ticketId ) );
	}

	/**
	 * @param string $ticketId
	 * @param bool   $expectedResult
	 *
	 * @throws \Fortuneglobe\Types\Exceptions\InvalidArgumentException
	 * @throws \InvalidArgumentException
	 * @throws \PHPUnit\Framework\ExpectationFailedException
	 * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
	 *
	 * @dataProvider allowedTicketNameProvider
	 */
	public function testCanCheckIfDiscountIsAllowedForACertainTicketName(
		string $ticketId,
		bool $expectedResult
	) : void
	{
		$discountName        = new DiscountName( 'Test discount' );
		$discountCode        = new DiscountCode( 'P95318357E' );
		$discountDescription = new DiscountDescription( 'Discount description' );
		$discountPrice       = new DiscountPrice( $this->getMoney( -3000 ) );
		$allowedticketIds    = new TicketId( 'PHPDD18-CT-01' );
		$allowedTickets      = [$allowedticketIds];

		$discountItem = new DiscountItem(
			$discountName,
			$discountCode,
			$discountDescription,
			$discountPrice,
			$allowedTickets
		);

		$this->assertSame( $expectedResult, $discountItem->isAllowedForTicket( new TicketId( $ticketId ) ) );
	}

	public function allowedTicketNameProvider() : array
	{
		return [
			[
				'ticketId'       => 'PHPDD18-CT-01',
				'expectedResult' => true,
			],
			[
				'ticketId'       => 'PHPDD18-WS-01',
				'expectedResult' => false,
			],
		];
	}
}
