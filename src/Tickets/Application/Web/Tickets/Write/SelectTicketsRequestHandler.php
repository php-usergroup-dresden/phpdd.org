<?php declare(strict_types=1);

namespace PHPUGDD\PHPDD\Website\Tickets\Application\Web\Tickets\Write;

use IceHawk\Forms\Feedback;
use IceHawk\IceHawk\Interfaces\HandlesPostRequest;
use IceHawk\IceHawk\Interfaces\ProvidesWriteRequestData;
use PHPUGDD\PHPDD\Website\Tickets\Application\Bridges\UserInput;
use PHPUGDD\PHPDD\Website\Tickets\Application\Configs\TicketsConfig;
use PHPUGDD\PHPDD\Website\Tickets\Application\Web\AbstractRequestHandler;
use PHPUGDD\PHPDD\Website\Tickets\Application\Web\Responses\Redirect;
use PHPUGDD\PHPDD\Website\Tickets\Application\Web\Tickets\Write\Validators\SelectTicketsValidator;
use PHPUGDD\PHPDD\Website\Tickets\Application\Web\Traits\CsrfTokenChecking;

final class SelectTicketsRequestHandler extends AbstractRequestHandler implements HandlesPostRequest
{
	use CsrfTokenChecking;

	private const FAIL_URL    = '/tickets/';

	private const SUCCESS_URL = '/tickets/details/';

	/**
	 * @param ProvidesWriteRequestData $request
	 *
	 * @throws \Exception
	 */
	public function handle( ProvidesWriteRequestData $request )
	{
		$input            = $request->getInput();
		$session          = $this->getEnv()->getSession();
		$errorHandler     = $this->getEnv()->getErrorHandler();
		$ticketSelectForm = $session->getTicketSelectionForm();
		$ticketSelectForm->resetFeedbacks();

		if ( $this->csrfCheckFailed( $ticketSelectForm, (string)$input->get( 'token' ), $errorHandler ) )
		{
			$ticketSelectForm->addFeedback( 'general', new Feedback( 'Invalid request. Please try again.' ) );

			(new Redirect())->respond( self::FAIL_URL );

			return;
		}

		$userInput = new UserInput( $input->getData() );
		$validator = new SelectTicketsValidator( $userInput, TicketsConfig::fromConfigFile() );

		if ( $validator->failed() )
		{
			foreach ( $validator->getMessages() as $groupKey => $messages )
			{
				$ticketSelectForm->addFeedback( $groupKey, new Feedback( implode( ' ', $messages ) ) );
			}

			(new Redirect())->respond( self::FAIL_URL );

			return;
		}

		$selectedTickets = $this->getSelectedTickets( $input->get( 'quantity' ) );
		$ticketSelectForm->setData( ['selectedTickets' => $selectedTickets] );

		(new Redirect())->respond( self::SUCCESS_URL );
	}

	private function getSelectedTickets( array $quantities ) : array
	{
		$selectedTickets = [];

		foreach ( $quantities as $id => $quantity )
		{
			if ( 0 !== (int)$quantity )
			{
				$selectedTickets[ $id ] = (int)$quantity;
			}
		}

		return $selectedTickets;
	}
}