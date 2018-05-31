<?php declare(strict_types=1);

namespace PHPUGDD\PHPDD\Website\Tickets\Application\Payments;

use PHPUGDD\PHPDD\Website\Tickets\Application\Exceptions\RuntimeException;
use PHPUGDD\PHPDD\Website\Tickets\Application\Payments\Constants\PaymentProviders;
use PHPUGDD\PHPDD\Website\Tickets\Application\Payments\Interfaces\CalculatesPaymentFee;
use PHPUGDD\PHPDD\Website\Tickets\Application\Payments\PaymentFeeCalculators\PaypalFeeCalculator;
use PHPUGDD\PHPDD\Website\Tickets\Application\Payments\PaymentFeeCalculators\StripeFeeCalculator;
use PHPUGDD\PHPDD\Website\Tickets\Application\Types\PaymentProvider;

final class PaymentFeeCalculatorFactory
{
	/**
	 * @param PaymentProvider $paymentProvider
	 *
	 * @throws RuntimeException
	 * @return CalculatesPaymentFee
	 */
	public function getCalculator( PaymentProvider $paymentProvider ) : CalculatesPaymentFee
	{
		switch ( $paymentProvider->toString() )
		{
			case PaymentProviders::PAYPAL:
				return new PaypalFeeCalculator();
				break;

			case PaymentProviders::STRIPE:
				return new StripeFeeCalculator();
				break;

			default:
				throw new RuntimeException( 'Payment provider not implemented.' );
		}
	}
}