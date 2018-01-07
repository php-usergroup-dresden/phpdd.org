<?php declare(strict_types=1);
/**
 * @author hollodotme
 */

namespace PHPUGDD\PHPDD\Website\Tests\Unit\Application\Types;

use Fortuneglobe\Types\Exceptions\InvalidArgumentException;
use Money\Money;
use PHPUGDD\PHPDD\Website\Application\Types\TicketOrderPaymentTotal;
use PHPUGDD\PHPDD\Website\Tests\Fixtures\Traits\MoneyProviding;
use PHPUnit\Framework\TestCase;

/**
 * Class TicketOrderPaymentTotalTest
 * @package PHPUGDD\PHPDD\Website\Tests\Unit\Application\Types
 */
final class TicketOrderPaymentTotalTest extends TestCase
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
		$ticketOrderPaymentTotal = new TicketOrderPaymentTotal( $money );

		$this->assertInstanceOf( TicketOrderPaymentTotal::class, $ticketOrderPaymentTotal );
		$this->assertSame( $money, $ticketOrderPaymentTotal->getMoney() );
	}

	public function validMoneyProvider() : array
	{
		return [
			[
				'money' => $this->getMoney( 0 ),
			],
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
	public function testThrowsExceptionForNegativeMoney() : void
	{
		$this->expectException( InvalidArgumentException::class );

		new TicketOrderPaymentTotal( $this->getMoney( -1000 ) );
	}
}
