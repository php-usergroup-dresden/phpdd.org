<?php declare(strict_types=1);

namespace PHPUGDD\PHPDD\Website\Tickets\Application\Payments\Interfaces;

interface ProvidesPaymentAuthorizationResult extends ProvidesActionResult
{
	public function getApprovalUrl() : string;
}