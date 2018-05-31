<?php declare(strict_types=1);

namespace PHPUGDD\PHPDD\Website\Tickets\Application\Payments;

use PHPUGDD\PHPDD\Website\Tickets\Application\Exceptions\RuntimeException;
use PHPUGDD\PHPDD\Website\Tickets\Application\Payments\Constants\PaymentProviders;
use PHPUGDD\PHPDD\Website\Tickets\Application\Payments\Interfaces\PaysTicketOrders;
use PHPUGDD\PHPDD\Website\Tickets\Application\Payments\PaymentServices\PaypalService;
use PHPUGDD\PHPDD\Website\Tickets\Application\Payments\PaymentServices\StripeService;
use PHPUGDD\PHPDD\Website\Tickets\Application\Types\PaymentProvider;
use PHPUGDD\PHPDD\Website\Tickets\Infrastructure\Configs\PaypalClientConfig;
use PHPUGDD\PHPDD\Website\Tickets\Infrastructure\RequiredInterfaces\Paypal\PaypalClient;

final class PaymentServiceFactory
{
	/**
	 * @param PaymentProvider $paymentProvider
	 *
	 * @throws RuntimeException
	 * @return PaysTicketOrders
	 */
	public function getPaymentService( PaymentProvider $paymentProvider ) : PaysTicketOrders
	{
		switch ( $paymentProvider->toString() )
		{
			case PaymentProviders::PAYPAL:
				$paypalClient = new PaypalClient( PaypalClientConfig::fromConfigFile() );

				return new PaypalService( $paypalClient );
				break;

			case PaymentProviders::STRIPE:
				return new StripeService();
				break;

			default:
				throw new RuntimeException( 'Payment service not implemented.' );
		}
	}
}