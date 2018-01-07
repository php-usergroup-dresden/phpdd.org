<?php declare(strict_types=1);
/**
 * @author hollodotme
 */

namespace PHPUGDD\PHPDD\Website\Tests\Unit\Application\Types;

use Fortuneglobe\Types\Exceptions\InvalidArgumentException;
use Money\Money;
use PHPUGDD\PHPDD\Website\Application\Types\TicketOrderDiscountTotal;
use PHPUGDD\PHPDD\Website\Tests\Fixtures\Traits\MoneyProviding;
use PHPUnit\Framework\TestCase;

/**
 * Class TicketOrderDiscountTotalTest
 * @package PHPUGDD\PHPDD\Website\Tests\Unit\Application\Types
 */
final class TicketOrderDiscountTotalTest extends TestCase
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
		$ticketOrderDiscountTotal = new TicketOrderDiscountTotal( $money );

		$this->assertInstanceOf( TicketOrderDiscountTotal::class, $ticketOrderDiscountTotal );
		$this->assertSame( $money, $ticketOrderDiscountTotal->getMoney() );
	}

	public function validMoneyProvider() : array
	{
		return [
			[
				'money' => $this->getMoney( 0 ),
			],
			[
				'money' => $this->getMoney( -1 ),
			],
			[
				'money' => $this->getMoney( -1000 ),
			],
		];
	}

	/**
	 * @throws \PHPUnit\Framework\Exception
	 */
	public function testThrowsExceptionForNegativeMoney() : void
	{
		$this->expectException( InvalidArgumentException::class );

		new TicketOrderDiscountTotal( $this->getMoney( 1000 ) );
	}
}
