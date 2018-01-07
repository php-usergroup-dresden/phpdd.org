<?php declare(strict_types=1);
/**
 * @author hollodotme
 */

namespace PHPUGDD\PHPDD\Website\Tests\Unit\Application\Types;

use Fortuneglobe\Types\Exceptions\InvalidArgumentException;
use Money\Money;
use PHPUGDD\PHPDD\Website\Application\Types\TicketPrice;
use PHPUGDD\PHPDD\Website\Tests\Fixtures\Traits\MoneyProviding;
use PHPUnit\Framework\TestCase;

/**
 * Class TicketPriceTest
 * @package PHPUGDD\PHPDD\Website\Tests\Unit\Application\Types
 */
final class TicketPriceTest extends TestCase
{
	use MoneyProviding;

	/**
	 * @param Money $money
	 *
	 * @throws \PHPUnit\Framework\Exception
	 * @dataProvider validMoneyProvider
	 */
	public function testCanCreateInstanceFromValidMoney( Money $money ) : void
	{
		$ticketPrice = new TicketPrice( $money );

		$this->assertInstanceOf( TicketPrice::class, $ticketPrice );
		$this->assertSame( $money, $ticketPrice->getMoney() );
	}

	public function validMoneyProvider() : array
	{
		return [
			[
				'money' => $this->getMoney( 1 ),
			],
			[
				'money' => $this->getMoney( 1000 ),
			],
		];
	}

	/**
	 * @throws \PHPUnit\Framework\Exception
	 */
	public function testThrowsExceptionForZeroMoney() : void
	{
		$this->expectException( InvalidArgumentException::class );

		new TicketPrice( $this->getMoney( 0 ) );
	}

	/**
	 * @throws \PHPUnit\Framework\Exception
	 */
	public function testThrowsExceptionForNegativeMoney() : void
	{
		$this->expectException( InvalidArgumentException::class );

		new TicketPrice( $this->getMoney( -1000 ) );
	}
}
