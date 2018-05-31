<?php declare(strict_types=1);

namespace PHPUGDD\PHPDD\Website\Tests\Tickets\Unit\Application\Payments\PaymentFeeCalculators;

use PHPUGDD\PHPDD\Website\Tickets\Application\Constants\CountryCodes;
use PHPUGDD\PHPDD\Website\Tickets\Application\Payments\PaymentFeeCalculators\StripeFeeCalculator;
use PHPUGDD\PHPDD\Website\Tickets\Application\Types\CountryCode;
use PHPUGDD\PHPDD\Website\Tickets\Traits\MoneyProviding;
use PHPUnit\Framework\TestCase;

final class StripeFeeCalculatorTest extends TestCase
{
	use MoneyProviding;

	/**
	 * @param int    $total
	 * @param string $expectedFee
	 *
	 * @throws \InvalidArgumentException
	 * @throws \PHPUnit\Framework\ExpectationFailedException
	 * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
	 *
	 * @dataProvider germanTotalProvider
	 */
	public function testGetPaymentFeeForGermany( int $total, string $expectedFee ) : void
	{
		$totalMoney  = $this->getMoney( $total );
		$countryCode = new CountryCode( CountryCodes::DE_SHORT );

		$calculator = new StripeFeeCalculator();

		$this->assertSame( $expectedFee, $calculator->getPaymentFee( $totalMoney, $countryCode )->getAmount() );
	}

	public function germanTotalProvider() : array
	{
		return [
			[
				'total'       => 100,
				'expectedFee' => '26',
			],
			[
				'total'       => 1000,
				'expectedFee' => '39',
			],
			[
				'total'       => 10000,
				'expectedFee' => '167',
			],
		];
	}

	/**
	 * @param int    $total
	 * @param string $expectedFee
	 *
	 * @throws \InvalidArgumentException
	 * @throws \PHPUnit\Framework\ExpectationFailedException
	 * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
	 *
	 * @dataProvider otherCountryTotalProvider
	 */
	public function testGetPaymentFeeForOtherCountry( int $total, string $expectedFee ) : void
	{
		$totalMoney  = $this->getMoney( $total );
		$countryCode = new CountryCode( CountryCodes::US_SHORT );

		$calculator = new StripeFeeCalculator();

		$this->assertSame( $expectedFee, $calculator->getPaymentFee( $totalMoney, $countryCode )->getAmount() );
	}

	public function otherCountryTotalProvider() : array
	{
		return [
			[
				'total'       => 100,
				'expectedFee' => '28',
			],
			[
				'total'       => 1000,
				'expectedFee' => '55',
			],
			[
				'total'       => 10000,
				'expectedFee' => '324',
			],
		];
	}
}
