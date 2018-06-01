<?php declare(strict_types=1);

namespace PHPUGDD\PHPDD\Website\Tickets\Application\Payments\PaymentServices;

use PayPal\Api\Payment;
use PHPUGDD\PHPDD\Website\Tickets\Application\Payments\Interfaces\ProvidesPaymentExecutionResult;
use PHPUGDD\PHPDD\Website\Tickets\Application\Types\PaymentId;

final class PaymentExecutionResult extends AbstractResult implements ProvidesPaymentExecutionResult
{
	/** @var PaymentId */
	private $paymentId;

	public static function fromPaypalPayment( Payment $payment ) : self
	{
		$instance            = new self( ResultType::SUCCEEDED );
		$instance->paymentId = new PaymentId( $payment->getId() );

		return $instance;
	}

	public function getPaymentId() : PaymentId
	{
		return $this->paymentId;
	}
}