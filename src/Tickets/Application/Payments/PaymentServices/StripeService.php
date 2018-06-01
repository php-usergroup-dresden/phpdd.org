<?php declare(strict_types=1);

namespace PHPUGDD\PHPDD\Website\Tickets\Application\Payments\PaymentServices;

use PHPUGDD\PHPDD\Website\Tickets\Application\Payments\Interfaces\PaysTicketOrders;
use PHPUGDD\PHPDD\Website\Tickets\Application\Payments\Interfaces\ProvidesPaymentAuthorizationResult;
use PHPUGDD\PHPDD\Website\Tickets\Application\Payments\Interfaces\ProvidesPaymentExecutionResult;
use PHPUGDD\PHPDD\Website\Tickets\Application\Tickets\TicketOrder;
use PHPUGDD\PHPDD\Website\Tickets\Application\Types\PayerId;
use PHPUGDD\PHPDD\Website\Tickets\Application\Types\PaymentId;

final class StripeService implements PaysTicketOrders
{
	public function authorize( TicketOrder $ticketOrder ) : ProvidesPaymentAuthorizationResult
	{
		return new PaymentAuthorizationResult( ResultType::FAILED, 'Not implemented, yet.' );
	}

	public function execute( PaymentId $paymentId, PayerId $payerId ) : ProvidesPaymentExecutionResult
	{
		return new PaymentExecutionResult( ResultType::FAILED, 'Not implemented, yet.' );
	}
}