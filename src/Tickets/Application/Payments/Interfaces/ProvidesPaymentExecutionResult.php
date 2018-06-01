<?php declare(strict_types=1);

namespace PHPUGDD\PHPDD\Website\Tickets\Application\Payments\Interfaces;

use PHPUGDD\PHPDD\Website\Tickets\Application\Types\PaymentId;

interface ProvidesPaymentExecutionResult extends ProvidesActionResult
{
	public function getPaymentId() : PaymentId;
}