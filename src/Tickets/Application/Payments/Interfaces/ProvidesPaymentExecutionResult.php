<?php declare(strict_types=1);

namespace PHPUGDD\PHPDD\Website\Tickets\Application\Payments\Interfaces;

interface ProvidesPaymentExecutionResult extends ProvidesActionResult
{
	public function getPaymentId() : string;
}