<?php declare(strict_types=1);

namespace PHPUGDD\PHPDD\Website\Tickets\Infrastructure\RequiredInterfaces\Paypal\Exceptions;

use PayPal\Api\Payment;
use RuntimeException;

final class ExecutionOfPaypalPaymentFailed extends RuntimeException
{
	/** @var Payment */
	private $payment;

	public function getPayment() : Payment
	{
		return $this->payment;
	}

	public function withPayment( Payment $payment ) : ExecutionOfPaypalPaymentFailed
	{
		$this->payment = $payment;

		return $this;
	}
}
