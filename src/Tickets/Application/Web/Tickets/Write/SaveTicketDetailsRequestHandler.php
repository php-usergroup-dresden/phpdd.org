<?php declare(strict_types=1);

namespace PHPUGDD\PHPDD\Website\Tickets\Application\Web\Tickets\Write;

use IceHawk\Forms\Feedback;
use IceHawk\Forms\Form;
use IceHawk\IceHawk\Interfaces\HandlesPostRequest;
use IceHawk\IceHawk\Interfaces\ProvidesWriteRequestData;
use IceHawk\IceHawk\Interfaces\ProvidesWriteRequestInputData;
use PHPUGDD\PHPDD\Website\Tickets\Application\Bridges\UserInput;
use PHPUGDD\PHPDD\Website\Tickets\Application\Configs\DiscountsConfig;
use PHPUGDD\PHPDD\Website\Tickets\Application\Configs\TicketsConfig;
use PHPUGDD\PHPDD\Website\Tickets\Application\Tickets\SelectedTicketInfos;
use PHPUGDD\PHPDD\Website\Tickets\Application\Web\AbstractRequestHandler;
use PHPUGDD\PHPDD\Website\Tickets\Application\Web\Responses\Redirect;
use PHPUGDD\PHPDD\Website\Tickets\Application\Web\Tickets\Write\Interfaces\ValidatesUserInput;
use PHPUGDD\PHPDD\Website\Tickets\Application\Web\Tickets\Write\Validators\AttendeeValidator;
use PHPUGDD\PHPDD\Website\Tickets\Application\Web\Tickets\Write\Validators\BillingInformationValidator;
use PHPUGDD\PHPDD\Website\Tickets\Application\Web\Tickets\Write\Validators\CompositeValidator;
use PHPUGDD\PHPDD\Website\Tickets\Application\Web\Tickets\Write\Validators\DiscountCodeValidator;
use PHPUGDD\PHPDD\Website\Tickets\Application\Web\Traits\CsrfTokenChecking;
use function implode;

final class SaveTicketDetailsRequestHandler extends AbstractRequestHandler implements HandlesPostRequest
{
	use CsrfTokenChecking;

	private const FAIL_URL              = '/tickets/details/';

	private const SUCESS_URL            = '/tickets/payment/';

	private const GENERAL_ERROR_MESSAGE = 'Your data is not valid yet. Please check the red error messages.';

	public function handle( ProvidesWriteRequestData $request )
	{
		$input               = $request->getInput();
		$session             = $this->getEnv()->getSession();
		$ticketSelectionForm = $session->getTicketSelectionForm();
		$ticketDetailsForm   = $session->getTicketDetailsForm();
		$errorHandler        = $this->getEnv()->getErrorHandler();

		$ticketDetailsForm->resetFeedbacks();
		$ticketDetailsForm->setData( $input->getData() );

		if ( $this->csrfCheckFailed( $ticketDetailsForm, (string)$input->get( 'token' ), $errorHandler ) )
		{
			$ticketDetailsForm->addFeedback( 'general', new Feedback( 'Invalid request. Please try again.' ) );

			(new Redirect())->respond( self::FAIL_URL );

			return;
		}

		$ticketsConfig       = TicketsConfig::fromConfigFile();
		$selectedTickets     = (array)$ticketSelectionForm->get( 'selectedTickets' );
		$selectedTicketInfos = new SelectedTicketInfos( $ticketsConfig, $selectedTickets );
		$discountConfigs     = DiscountsConfig::fromConfigFile();

		$userInputValidator = $this->getUserInputValidator( $selectedTicketInfos, $discountConfigs, $input );

		if ( $userInputValidator->failed() )
		{
			$ticketDetailsForm->addFeedback( 'general', new Feedback( self::GENERAL_ERROR_MESSAGE ) );
			$this->addValidatorMessagesToForm( $ticketDetailsForm, $userInputValidator );

			(new Redirect())->respond( self::FAIL_URL );

			return;
		}

		(new Redirect())->respond( self::SUCESS_URL );
	}

	private function getUserInputValidator(
		SelectedTicketInfos $selectedTicketInfos,
		DiscountsConfig $discountsConfig,
		ProvidesWriteRequestInputData $input
	) : ValidatesUserInput
	{
		$userInputValidator = new CompositeValidator();
		$userInputValidator->add( new BillingInformationValidator( new UserInput( $input->getData() ) ) );

		$attendees = (array)$input->get( 'attendees', [] );
		$discounts = (array)$input->get( 'discounts', [] );

		foreach ( $selectedTicketInfos->getTickets() as $selectedTicketInfo )
		{
			for ( $i = 0; $i < $selectedTicketInfo->getQuantity(); $i++ )
			{
				$ticketId = $selectedTicketInfo->getId()->toString();

				$attendeeName      = $attendees[ $ticketId ][ $i ] ?? '';
				$attendeeUserInput = new UserInput( ['attendeeName' => $attendeeName] );

				$userInputValidator->add( new AttendeeValidator( $attendeeUserInput, $ticketId, $i ) );

				$discountCode      = $discounts[ $ticketId ][ $i ] ?? '';
				$discountUserInput = new UserInput( ['discountCode' => $discountCode] );

				$userInputValidator->add(
					new DiscountCodeValidator( $discountUserInput, $discountsConfig, $ticketId, $i )
				);
			}
		}

		return $userInputValidator;
	}

	private function addValidatorMessagesToForm( Form $form, ValidatesUserInput $validator ) : void
	{
		foreach ( $validator->getMessages() as $groupKey => $messages )
		{
			$form->addFeedback( $groupKey, new Feedback( implode( ' ', $messages ) ) );
		}
	}
}