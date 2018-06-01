<?php declare(strict_types=1);

namespace PHPUGDD\PHPDD\Website\Tickets\Application\Web\Tickets\Write;

use DateTimeImmutable;
use IceHawk\Forms\Feedback;
use IceHawk\Forms\Form;
use IceHawk\IceHawk\Interfaces\HandlesPostRequest;
use IceHawk\IceHawk\Interfaces\ProvidesWriteRequestData;
use PHPUGDD\PHPDD\Website\Tickets\Application\Bridges\UserInput;
use PHPUGDD\PHPDD\Website\Tickets\Application\Configs\DiscountsConfig;
use PHPUGDD\PHPDD\Website\Tickets\Application\Configs\TicketsConfig;
use PHPUGDD\PHPDD\Website\Tickets\Application\Exceptions\RuntimeException;
use PHPUGDD\PHPDD\Website\Tickets\Application\Payments\Constants\PaymentProviders;
use PHPUGDD\PHPDD\Website\Tickets\Application\Payments\PaymentServiceFactory;
use PHPUGDD\PHPDD\Website\Tickets\Application\Tickets\Repositories\TicketOrderRepository;
use PHPUGDD\PHPDD\Website\Tickets\Application\Tickets\TicketOrderBuilder;
use PHPUGDD\PHPDD\Website\Tickets\Application\Tickets\TicketOrderPayment;
use PHPUGDD\PHPDD\Website\Tickets\Application\Types\PayerId;
use PHPUGDD\PHPDD\Website\Tickets\Application\Types\PaymentId;
use PHPUGDD\PHPDD\Website\Tickets\Application\Types\PaymentProvider;
use PHPUGDD\PHPDD\Website\Tickets\Application\Types\TicketOrderId;
use PHPUGDD\PHPDD\Website\Tickets\Application\Web\AbstractRequestHandler;
use PHPUGDD\PHPDD\Website\Tickets\Application\Web\Responses\HtmlPage;
use PHPUGDD\PHPDD\Website\Tickets\Application\Web\Responses\Redirect;
use PHPUGDD\PHPDD\Website\Tickets\Application\Web\Tickets\Write\Interfaces\ValidatesUserInput;
use PHPUGDD\PHPDD\Website\Tickets\Application\Web\Tickets\Write\Validators\PaypalSuccessValidator;
use Throwable;
use function dirname;

final class PaypalSuccessRequestHandler extends AbstractRequestHandler implements HandlesPostRequest
{
	private const FAIL_URL_DETAILS = '/tickets/details/';

	private const FAIL_URL_PAYMENT = '/tickets/payment/';

	/**
	 * @param ProvidesWriteRequestData $request
	 *
	 * @throws RuntimeException
	 * @throws \Exception
	 * @throws Throwable
	 */
	public function handle( ProvidesWriteRequestData $request )
	{
		$input               = $request->getInput();
		$session             = $this->getEnv()->getSession();
		$ticketDetailsForm   = $session->getTicketDetailsForm();
		$ticketSelectionForm = $session->getTicketSelectionForm();
		$ticketPaymentForm   = $session->getTicketPaymentForm();
		$selectedTickets     = $ticketSelectionForm->get( 'selectedTickets' );
		$ticketOrderId       = (string)$ticketSelectionForm->get( 'ticketOrderId' );
		$ticketDetails       = $ticketDetailsForm->getData();
		$ticketsConfig       = TicketsConfig::fromConfigFile();
		$discountsConfig     = DiscountsConfig::fromConfigFile();
		$ticketOrderBuilder  = new TicketOrderBuilder( $ticketsConfig, $discountsConfig );
		$ticketOrder         = $ticketOrderBuilder->buildFromInputData(
			$ticketOrderId,
			$selectedTickets,
			$ticketDetails
		);

		$ticketDetailsForm->resetFeedbacks();
		$ticketPaymentForm->resetFeedbacks();

		$userInput = new UserInput( $input->getData() );
		$validator = new PaypalSuccessValidator( $userInput );

		if ( $validator->failed() )
		{
			$this->addValidatorMessagesToForm( $ticketDetailsForm, $validator );

			(new Redirect())->respond( self::FAIL_URL_DETAILS );

			return;
		}

		$paymentProvider = new PaymentProvider( PaymentProviders::PAYPAL );
		$payerId         = new PayerId( (string)$input->get( 'PayerID' ) );
		$paymentId       = new PaymentId( (string)$input->get( 'paymentId' ) );
		$database        = $this->getEnv()->getDatabase();

		$ticketOrderPayment = new TicketOrderPayment(
			$paymentProvider,
			$paymentId,
			$payerId,
			[
				'token' => (string)$input->get( 'token' ),
			]
		);

		$ticketOrder->assignPayment( $ticketOrderPayment );

		try
		{
			$ticketOrderRepository = new TicketOrderRepository( $database );
			$ticketOrderRepository->placeTicketOrder( $ticketOrder );
		}
		catch ( Throwable $e )
		{
			$this->getEnv()->getErrorHandler()->captureException( $e );

			$ticketPaymentForm->addFeedback(
				'general',
				new Feedback(
					'There was an error placing your ticket order. 
					Please try again later. No payments were executed.'
				)
			);

			(new Redirect())->respond( self::FAIL_URL_PAYMENT );

			return;
		}

		$paymentService = (new PaymentServiceFactory())->getPaymentService( $paymentProvider );

		$execResult = $paymentService->execute( $paymentId, $payerId, $ticketOrder->getPaymentTotal() );

		if ( $execResult->failed() )
		{
			$ticketOrderRepository->removeTicketOrder( $ticketOrder->getOrderId() );

			$ticketDetailsForm->addFeedback(
				'paymentProvider',
				new Feedback(
					'There was an error executing your payment. 
					Please try again later or use another payment provider.'
				)
			);

			(new Redirect())->respond( self::FAIL_URL_DETAILS );

			return;
		}

		$this->markPaymentAsExecuted( $ticketOrderRepository, $execResult->getPaymentId() );

		$this->prepareDonePage( $ticketOrder->getOrderId() );

		$redirectUrl = sprintf( '/tickets/done/%s', $ticketOrder->getOrderId()->toString() );

		$session->resetTicketOrder();

		(new Redirect())->respond( $redirectUrl );
	}

	private function addValidatorMessagesToForm( Form $form, ValidatesUserInput $validator ) : void
	{
		foreach ( $validator->getMessages() as $groupKey => $messages )
		{
			$form->addFeedback( $groupKey, new Feedback( implode( ' ', $messages ) ) );
		}
	}

	private function markPaymentAsExecuted( TicketOrderRepository $ticketOrderRepository, PaymentId $paymentId ) : void
	{
		try
		{
			$ticketOrderRepository->markPaymentAsExecuted( $paymentId, new DateTimeImmutable() );
		}
		catch ( Throwable $e )
		{
			$this->getEnv()->getErrorHandler()->captureException( $e );
		}
	}

	/**
	 * @param TicketOrderId $ticketOrderId
	 *
	 * @throws \Exception
	 * @return bool
	 */
	private function prepareDonePage( TicketOrderId $ticketOrderId ) : bool
	{
		$data = [
			'ticketOrderId' => $ticketOrderId->toString(),
			'orderDate'     => (new DateTimeImmutable())->format( 'r' ),
		];

		$filePath = sprintf(
			'%s/%s.html',
			dirname( __DIR__, 6 ) . '/data/static/done',
			$ticketOrderId->toString()
		);

		return (new HtmlPage( $this->getEnv() ))->saveToFile(
			$filePath,
			'Tickets/Read/Pages/Done.twig',
			$data
		);
	}
}