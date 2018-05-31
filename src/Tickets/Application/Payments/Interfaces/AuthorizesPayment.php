<?php declare(strict_types=1);

namespace PHPUGDD\PHPDD\Website\Tickets\Application\Payments\Interfaces;

use PHPUGDD\PHPDD\Website\Tickets\Application\Tickets\TicketOrder;

interface AuthorizesPayment
{
	public function authorize(TicketOrder $ticketOrder) : ProvidesPaymentAuthorizationResult;
}