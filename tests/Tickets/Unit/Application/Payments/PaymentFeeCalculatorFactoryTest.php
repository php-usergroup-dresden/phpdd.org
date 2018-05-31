<?php declare(strict_types=1);

namespace PHPUGDD\PHPDD\Website\Tests\Tickets\Unit\Application\Payments;

use PHPUGDD\PHPDD\Website\Tickets\Application\Payments\Constants\PaymentProviders;
use PHPUGDD\PHPDD\Website\Tickets\Application\Payments\PaymentFeeCalculatorFactory;
use PHPUGDD\PHPDD\Website\Tickets\Application\Payments\PaymentFeeCalculators\PaypalFeeCalculator;
use PHPUGDD\PHPDD\Website\Tickets\Application\Payments\PaymentFeeCalculators\StripeFeeCalculator;
use PHPUGDD\PHPDD\Website\Tickets\Application\Types\PaymentProvider;
use PHPUnit\Framework\TestCase;

final class PaymentFeeCalculatorFactoryTest extends TestCase
{

	/**
	 * @param string $paymentProvider
	 * @param string $expectedCalculatorClass
	 *
	 * @throws \PHPUGDD\PHPDD\Website\Tickets\Application\Exceptions\RuntimeException
	 * @throws \PHPUnit\Framework\ExpectationFailedException
	 * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
	 *
	 * @dataProvider paymentProvider
	 */
	public function testCanGetCalculatorForPaymentProvider(
		string $paymentProvider,
		string $expectedCalculatorClass
	) : void
	{
		$factory    = new PaymentFeeCalculatorFactory();
		$calculator = $factory->getCalculator( new PaymentProvider( $paymentProvider ) );

		$this->assertInstanceOf( $expectedCalculatorClass, $calculator );
	}

	public function paymentProvider() : array
	{
		return [
			[
				'paymentProvider'         => PaymentProviders::PAYPAL,
				'expectedCalculatorClass' => PaypalFeeCalculator::class,
			],
			[
				'paymentProvider'         => PaymentProviders::STRIPE,
				'expectedCalculatorClass' => StripeFeeCalculator::class,
			],
		];
	}
}
