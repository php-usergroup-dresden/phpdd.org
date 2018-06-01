<?php declare(strict_types=1);

namespace PHPUGDD\PHPDD\Website\Tickets\Application\Payments\Interfaces;

use PHPUGDD\PHPDD\Website\Tickets\Application\Types\PayerId;
use PHPUGDD\PHPDD\Website\Tickets\Application\Types\PaymentId;

interface ExecutesPayment
{
	public function execute( PaymentId $paymentId, PayerId $payerId ) : ProvidesPaymentExecutionResult;
}