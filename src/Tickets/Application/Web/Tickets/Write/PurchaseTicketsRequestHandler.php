<?php declare(strict_types=1);

namespace PHPUGDD\PHPDD\Website\Tickets\Application\Web\Tickets\Write;

use IceHawk\Forms\Feedback;
use IceHawk\Forms\Form;
use IceHawk\IceHawk\Interfaces\HandlesPostRequest;
use IceHawk\IceHawk\Interfaces\ProvidesWriteRequestData;
use PHPUGDD\PHPDD\Website\Tickets\Application\Bridges\UserInput;
use PHPUGDD\PHPDD\Website\Tickets\Application\Configs\DiscountsConfig;
use PHPUGDD\PHPDD\Website\Tickets\Application\Configs\Exceptions\DiscountConfigNotFoundException;
use PHPUGDD\PHPDD\Website\Tickets\Application\Configs\TicketsConfig;
use PHPUGDD\PHPDD\Website\Tickets\Application\Exceptions\RuntimeException;
use PHPUGDD\PHPDD\Website\Tickets\Application\Payments\PaymentServiceFactory;
use PHPUGDD\PHPDD\Website\Tickets\Application\Tickets\Exceptions\AllowedTicketCountExceededException;
use PHPUGDD\PHPDD\Website\Tickets\Application\Tickets\Exceptions\AllowedTicketCountPerAttendeeExceededException;
use PHPUGDD\PHPDD\Website\Tickets\Application\Tickets\Exceptions\DiscountExceededTicketPriceException;
use PHPUGDD\PHPDD\Website\Tickets\Application\Tickets\Exceptions\DiscountNotAllowedForTicketException;
use PHPUGDD\PHPDD\Website\Tickets\Application\Tickets\TicketOrderBuilder;
use PHPUGDD\PHPDD\Website\Tickets\Application\Web\AbstractRequestHandler;
use PHPUGDD\PHPDD\Website\Tickets\Application\Web\Responses\Redirect;
use PHPUGDD\PHPDD\Website\Tickets\Application\Web\Tickets\Write\Interfaces\ValidatesUserInput;
use PHPUGDD\PHPDD\Website\Tickets\Application\Web\Tickets\Write\Validators\PurchaseOptInsValidator;
use PHPUGDD\PHPDD\Website\Tickets\Application\Web\Traits\CsrfTokenChecking;

final class PurchaseTicketsRequestHandler extends AbstractRequestHandler implements HandlesPostRequest
{
	use CsrfTokenChecking;

	private const FAIL_URL_DETAILS = '/tickets/details/';

	private const FAIL_URL_PAYMENT = '/tickets/payment/';

	/**
	 * @param ProvidesWriteRequestData $request
	 *
	 * @throws \Fortuneglobe\Types\Exceptions\InvalidArgumentException
	 * @throws \InvalidArgumentException
	 * @throws DiscountConfigNotFoundException
	 * @throws RuntimeException
	 * @throws AllowedTicketCountExceededException
	 * @throws AllowedTicketCountPerAttendeeExceededException
	 * @throws DiscountExceededTicketPriceException
	 * @throws DiscountNotAllowedForTicketException
	 */
	public function handle( ProvidesWriteRequestData $request )
	{
		$input               = $request->getInput();
		$session             = $this->getEnv()->getSession();
		$errorHandler        = $this->getEnv()->getErrorHandler();
		$ticketSelectionForm = $session->getTicketSelectionForm();
		$ticketDetailsForm   = $session->getTicketDetailsForm();
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

		$ticketPaymentForm->resetFeedbacks();
		$ticketDetailsForm->resetFeedbacks();

		$ticketPaymentForm->setData( $input->getData() );

		if ( $this->csrfCheckFailed( $ticketPaymentForm, (string)$input->get( 'token' ), $errorHandler ) )
		{
			$ticketPaymentForm->addFeedback(
				'general',
				new Feedback( 'Invalid request. Please try again.' )
			);

			(new Redirect())->respond( self::FAIL_URL_PAYMENT );

			return;
		}

		$validator = new PurchaseOptInsValidator( new UserInput( $input->getData() ) );

		if ( $validator->failed() )
		{
			$this->addValidatorMessagesToForm( $ticketPaymentForm, $validator );
			(new Redirect())->respond( self::FAIL_URL_PAYMENT );

			return;
		}

		$paymentProvider = $ticketOrder->getPaymentProvider();

		if ( null === $paymentProvider )
		{
			$ticketDetailsForm->addFeedback( 'paymentProvider', new Feedback( 'Please select a payment provider.' ) );

			(new Redirect())->respond( self::FAIL_URL_DETAILS );

			return;
		}

		$paymentService = (new PaymentServiceFactory())->getPaymentService( $paymentProvider );
		$authResult     = $paymentService->authorize( $ticketOrder );

		if ( $authResult->failed() )
		{
			$ticketPaymentForm->addFeedback(
				'general',
				new Feedback(
					'There was an error authorizing your payment. 
					Please try again later or use another payment provider.'
				)
			);

			(new Redirect())->respond( self::FAIL_URL_PAYMENT );

			return;
		}

		(new Redirect())->respond( $authResult->getApprovalUrl() );
	}

	private function addValidatorMessagesToForm( Form $form, ValidatesUserInput $validator ) : void
	{
		foreach ( $validator->getMessages() as $groupKey => $messages )
		{
			$form->addFeedback( $groupKey, new Feedback( implode( ' ', $messages ) ) );
		}
	}
}