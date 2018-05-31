<?php declare(strict_types=1);

namespace PHPUGDD\PHPDD\Website\Tests\Tickets\Unit\Application\Types;

use PHPUGDD\PHPDD\Website\Tickets\Application\Exceptions\InvalidArgumentException;
use PHPUGDD\PHPDD\Website\Tickets\Application\Types\PaymentFee;
use PHPUGDD\PHPDD\Website\Tickets\Traits\MoneyProviding;
use PHPUnit\Framework\TestCase;

final class PaymentFeeTest extends TestCase
{
	use MoneyProviding;

	/**
	 * @throws \InvalidArgumentException
	 * @throws \PHPUGDD\PHPDD\Website\Tickets\Application\Exceptions\InvalidArgumentException
	 * @throws \PHPUnit\Framework\ExpectationFailedException
	 * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
	 */
	public function testCanGetMoney() : void
	{
		$money = $this->getMoney( 100 );
		$fee   = new PaymentFee( $money );

		$this->assertSame( $money, $fee->getMoney() );
	}

	/**
	 * @throws InvalidArgumentException
	 * @throws \InvalidArgumentException
	 */
	public function testThrowsExceptionWhenConstructedWithNegativeMoney() : void
	{
		$this->expectException( InvalidArgumentException::class );
		$this->expectExceptionMessage( 'Invalid money amount for payment fee provided: -100' );

		new PaymentFee( $this->getMoney( -100 ) );
	}
}
