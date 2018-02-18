<?php declare(strict_types=1);
/**
 * @author hollodotme
 */

namespace PHPUGDD\PHPDD\Website\Tests\Tickets\Unit\Application\Types;

use Fortuneglobe\Types\Exceptions\InvalidArgumentException;
use Money\Money;
use PHPUGDD\PHPDD\Website\Tickets\Application\Types\DiscountPrice;
use PHPUGDD\PHPDD\Website\Tickets\Traits\MoneyProviding;
use PHPUnit\Framework\TestCase;

/**
 * Class DiscountPriceTest
 * @package PHPUGDD\PHPDD\Website\Tests\Tickets\Unit\Application\Types
 */
final class DiscountPriceTest extends TestCase
{
	use MoneyProviding;

	/**
	 * @param Money $money
	 *
	 * @throws \Fortuneglobe\Types\Exceptions\InvalidArgumentException
	 * @throws \PHPUnit\Framework\ExpectationFailedException
	 * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
	 * @dataProvider validDiscountMoneyProvider
	 */
	public function testCanCreateInstanceFromValidMoney( Money $money ) : void
	{
		$discountPrice = new DiscountPrice( $money );

		$this->assertInstanceOf( DiscountPrice::class, $discountPrice );
		$this->assertSame( $money, $discountPrice->getMoney() );
	}

	/**
	 * @return array
	 * @throws \InvalidArgumentException
	 */
	public function validDiscountMoneyProvider() : array
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
	public function testThrowsExceptionForPositiveMoneyAmount() : void
	{
		$this->expectException( InvalidArgumentException::class );

		new DiscountPrice( $this->getMoney( 1000 ) );
	}
}
