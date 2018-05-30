<?php declare(strict_types=1);

namespace PHPUGDD\PHPDD\Website\Tests\Tickets\Unit\Application\Tickets;

use PHPUGDD\PHPDD\Website\Tickets\Application\Tickets\DiscountItem;
use PHPUGDD\PHPDD\Website\Tickets\Application\Types\DiscountCode;
use PHPUGDD\PHPDD\Website\Tickets\Application\Types\DiscountDescription;
use PHPUGDD\PHPDD\Website\Tickets\Application\Types\DiscountName;
use PHPUGDD\PHPDD\Website\Tickets\Application\Types\DiscountPrice;
use PHPUGDD\PHPDD\Website\Tickets\Application\Types\TicketName;
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
		$ticketName          = new TicketName( 'Conference ticket' );

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
		$this->assertFalse( $discountItem->isAllowedForTicket( $ticketName ) );
	}

	/**
	 * @param string $ticketName
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
		string $ticketName,
		bool $expectedResult
	) : void
	{
		$discountName        = new DiscountName( 'Test discount' );
		$discountCode        = new DiscountCode( 'P95318357E' );
		$discountDescription = new DiscountDescription( 'Discount description' );
		$discountPrice       = new DiscountPrice( $this->getMoney( -3000 ) );
		$allowedticketName   = new TicketName( 'Conference ticket' );
		$allowedTickets      = [$allowedticketName];

		$discountItem = new DiscountItem(
			$discountName,
			$discountCode,
			$discountDescription,
			$discountPrice,
			$allowedTickets
		);

		$this->assertSame( $expectedResult, $discountItem->isAllowedForTicket( new TicketName( $ticketName ) ) );
	}

	public function allowedTicketNameProvider() : array
	{
		return [
			[
				'ticketName'     => 'Conference ticket',
				'expectedResult' => true,
			],
			[
				'ticketName'     => 'Workshop ticket',
				'expectedResult' => false,
			],
		];
	}
}
