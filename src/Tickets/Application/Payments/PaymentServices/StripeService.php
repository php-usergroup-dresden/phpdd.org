<?php declare(strict_types=1);

namespace PHPUGDD\PHPDD\Website\Tickets\Application\Payments\PaymentServices;

use PHPUGDD\PHPDD\Website\Tickets\Application\Payments\Interfaces\PaysTicketOrders;
use PHPUGDD\PHPDD\Website\Tickets\Application\Payments\Interfaces\ProvidesPaymentAuthorizationResult;
use PHPUGDD\PHPDD\Website\Tickets\Application\Payments\Interfaces\ProvidesPaymentExecutionResult;
use PHPUGDD\PHPDD\Website\Tickets\Application\Tickets\TicketOrder;

final class StripeService implements PaysTicketOrders
{
	public function authorize( TicketOrder $ticketOrder ) : ProvidesPaymentAuthorizationResult
	{
		// TODO: Implement authorize() method.
	}

	public function execute() : ProvidesPaymentExecutionResult
	{
		// TODO: Implement execute() method.
	}
}