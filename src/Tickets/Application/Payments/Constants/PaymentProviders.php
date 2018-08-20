<?php declare(strict_types=1);

namespace PHPUGDD\PHPDD\Website\Tickets\Application\Payments\Constants;

abstract class PaymentProviders
{
	public const PAYPAL        = 'PayPal';

	public const STRIPE        = 'Stripe';

	public const BANK_ACCOUNT  = 'Bank account';

	public const NONE          = 'None';

	public const ALL           = [
		self::PAYPAL,
		self::STRIPE,
		self::BANK_ACCOUNT,
		self::NONE,
	];

	public const TRANSFERABLES = [
		self::PAYPAL,
		self::STRIPE,
		self::BANK_ACCOUNT,
	];
}