<?php declare(strict_types=1);

namespace PHPUGDD\PHPDD\Website\Tickets\Application\Payments\PaymentServices;

use PayPal\Api\Payment;
use PHPUGDD\PHPDD\Website\Tickets\Application\Payments\Interfaces\ProvidesPaymentAuthorizationResult;

final class PaymentAuthorizationResult extends AbstractResult implements ProvidesPaymentAuthorizationResult
{
	/** @var string */
	private $approvalUrl;

	public static function fromPaypalPayment( Payment $paypalPayment ) : self
	{
		$instance              = new self( ResultType::SUCCEEDED );
		$instance->approvalUrl = $paypalPayment->getApprovalLink();

		return $instance;
	}

	public function getApprovalUrl() : string
	{
		return $this->approvalUrl;
	}
}