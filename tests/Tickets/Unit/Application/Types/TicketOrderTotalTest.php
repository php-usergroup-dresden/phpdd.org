<?php declare(strict_types=1);
/**
 * @author hollodotme
 */

namespace PHPUGDD\PHPDD\Website\Tests\Tickets\Unit\Application\Types;

use Fortuneglobe\Types\Exceptions\InvalidArgumentException;
use Money\Money;
use PHPUGDD\PHPDD\Website\Tickets\Application\Types\TicketOrderTotal;
use PHPUGDD\PHPDD\Website\Tickets\Traits\MoneyProviding;
use PHPUnit\Framework\TestCase;

/**
 * Class TicketOrderTotalTest
 * @package PHPUGDD\PHPDD\Website\Tests\Tickets\Unit\Application\Types
 */
final class TicketOrderTotalTest extends TestCase
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
		$ticketOrderTotal = new TicketOrderTotal( $money );

		$this->assertInstanceOf( TicketOrderTotal::class, $ticketOrderTotal );
		$this->assertSame( $money, $ticketOrderTotal->getMoney() );
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
				'money' => $this->getMoney( 1 ),
			],
			[
				'money' => $this->getMoney( 1000 ),
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

		new TicketOrderTotal( $this->getMoney( -1000 ) );
	}
}
