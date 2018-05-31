<?php declare(strict_types=1);

namespace PHPUGDD\PHPDD\Website\Tickets\Application\Payments\Interfaces;

interface ExecutesPayment
{
	public function execute( string $paymentId, string $payerId ) : ProvidesPaymentExecutionResult;
}