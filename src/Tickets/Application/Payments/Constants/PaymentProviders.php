<?php declare(strict_types=1);

namespace PHPUGDD\PHPDD\Website\Tickets\Application\Payments\Constants;

abstract class PaymentProviders
{
	public const PAYPAL = 'paypal';

	public const STRIPE = 'stripe';

	public const ALL    = [
		self::PAYPAL,
		self::STRIPE,
	];
}