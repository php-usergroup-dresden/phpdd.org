<?php declare(strict_types=1);

namespace PHPUGDD\PHPDD\Website\Tickets\Infrastructure\RequiredInterfaces\Paypal;

final class PaypalExecuteRequest
{
	/** @var string */
	private $paymentId;

	/** @var string */
	private $payerId;

	public function __construct( string $paymentId, string $payerId )
	{
		$this->paymentId = $paymentId;
		$this->payerId   = $payerId;
	}

	public function getPaymentId() : string
	{
		return $this->paymentId;
	}

	public function getPayerId() : string
	{
		return $this->payerId;
	}
}
