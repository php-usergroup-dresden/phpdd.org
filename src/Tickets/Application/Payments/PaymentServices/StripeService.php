<?php declare(strict_types=1);

namespace PHPUGDD\PHPDD\Website\Tickets\Application\Payments\PaymentServices;

use PHPUGDD\PHPDD\Website\Tickets\Application\Payments\Interfaces\PaysTicketOrders;
use PHPUGDD\PHPDD\Website\Tickets\Application\Payments\Interfaces\ProvidesPaymentAuthorizationResult;
use PHPUGDD\PHPDD\Website\Tickets\Application\Payments\Interfaces\ProvidesPaymentExecutionResult;
use PHPUGDD\PHPDD\Website\Tickets\Application\Tickets\TicketOrder;
use PHPUGDD\PHPDD\Website\Tickets\Application\Types\PayerId;
use PHPUGDD\PHPDD\Website\Tickets\Application\Types\PaymentId;
use PHPUGDD\PHPDD\Website\Tickets\Application\Types\TicketOrderPaymentTotal;
use PHPUGDD\PHPDD\Website\Tickets\Infrastructure\RequiredInterfaces\Stripe\StripeClient;
use PHPUGDD\PHPDD\Website\Tickets\Infrastructure\RequiredInterfaces\Stripe\StripeExecuteRequest;
use Throwable;

final class StripeService implements PaysTicketOrders
{
	/** @var StripeClient */
	private $stripeClient;

	public function __construct( StripeClient $stripeClient )
	{
		$this->stripeClient = $stripeClient;
	}

	public function authorize( TicketOrder $ticketOrder ) : ProvidesPaymentAuthorizationResult
	{
		return new PaymentAuthorizationResult( ResultType::FAILED, 'Not implemented.' );
	}

	public function execute(
		PaymentId $paymentId,
		PayerId $payerId,
		TicketOrderPaymentTotal $paymentTotal
	) : ProvidesPaymentExecutionResult
	{
		$execRequest = new StripeExecuteRequest(
			$paymentId->toString(),
			'Ticket purchase for PHPDD18',
			$paymentTotal->getMoney()->getAmount(),
			$paymentTotal->getMoney()->getCurrency()->getCode()
		);

		try
		{
			$stripeCharge = $this->stripeClient->executePayment( $execRequest );

			return PaymentExecutionResult::fromStripeCharge( $stripeCharge );
		}
		catch ( \Stripe\Error\Card $e )
		{
			return new PaymentExecutionResult(
				ResultType::FAILED,
				'Your card was declined. Please use another payment provider.'
			);
		}
		catch ( \Stripe\Error\RateLimit $e )
		{
			return new PaymentExecutionResult(
				ResultType::FAILED,
				'We could not process your payment at the moment. Please try again later.'
			);
		}
		catch ( Throwable $e )
		{
			return new PaymentExecutionResult(
				ResultType::FAILED,
				'We could not process your payment at the moment. Please try again later.'
			);
		}
	}
}