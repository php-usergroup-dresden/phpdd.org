<?php declare(strict_types=1);
/**
 * @author hollodotme
 */

namespace PHPUGDD\PHPDD\Website\Tests\Tickets\Unit\Application\Types;

use Fortuneglobe\Types\Exceptions\InvalidArgumentException;
use Money\Money;
use PHPUGDD\PHPDD\Website\Tickets\Application\Types\TicketOrderDiscountTotal;
use PHPUGDD\PHPDD\Website\Tickets\Traits\MoneyProviding;
use PHPUnit\Framework\TestCase;

/**
 * Class TicketOrderDiscountTotalTest
 * @package PHPUGDD\PHPDD\Website\Tests\Tickets\Unit\Application\Types
 */
final class TicketOrderDiscountTotalTest extends TestCase
{
	use MoneyProviding;

	/**
	 * @param Money $money
	 *
	 * @throws \Fortuneglobe\Types\Exceptions\InvalidArgumentException
	 * @throws \PHPUnit\Framework\ExpectationFailedException
	 * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
	 * @dataProvider validMoneyProvider
	 */
	public function testCanCreateInstanceFromValidMoney( Money $money ) : void
	{
		$ticketOrderDiscountTotal = new TicketOrderDiscountTotal( $money );

		$this->assertInstanceOf( TicketOrderDiscountTotal::class, $ticketOrderDiscountTotal );
		$this->assertSame( $money, $ticketOrderDiscountTotal->getMoney() );
	}

	/**
	 * @return array
	 * @throws \InvalidArgumentException
	 */
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
	 * @throws \Fortuneglobe\Types\Exceptions\InvalidArgumentException
	 * @throws \InvalidArgumentException
	 */
	public function testThrowsExceptionForNegativeMoney() : void
	{
		$this->expectException( InvalidArgumentException::class );

		new TicketOrderDiscountTotal( $this->getMoney( 1000 ) );
	}
}
