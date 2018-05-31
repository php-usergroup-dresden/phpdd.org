<?php declare(strict_types=1);

namespace PHPUGDD\PHPDD\Website\Tickets\Application\Payments\PaymentServices;

use PayPal\Api\Payment;
use PHPUGDD\PHPDD\Website\Tickets\Application\Payments\Interfaces\ProvidesPaymentExecutionResult;

final class PaymentExecutionResult extends AbstractResult implements ProvidesPaymentExecutionResult
{
	/** @var string */
	private $paymentId;

	public static function fromPaypalPayment( Payment $payment ) : self
	{
		$instance            = new self( ResultType::SUCCEEDED );
		$instance->paymentId = $payment->getId();

		return $instance;
	}

	public function getPaymentId() : string
	{
		return $this->paymentId;
	}
}