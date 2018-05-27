<?php declare(strict_types=1);

namespace PHPUGDD\PHPDD\Website\Tickets\Application\Web\Tickets\Write;

use IceHawk\Forms\Exceptions\InvalidTokenString;
use IceHawk\Forms\Feedback;
use IceHawk\Forms\Form;
use IceHawk\Forms\Security\Token;
use IceHawk\IceHawk\Interfaces\HandlesPostRequest;
use IceHawk\IceHawk\Interfaces\ProvidesWriteRequestData;
use PHPUGDD\PHPDD\Website\Tickets\Application\Bridges\UserInput;
use PHPUGDD\PHPDD\Website\Tickets\Application\Configs\TicketsConfig;
use PHPUGDD\PHPDD\Website\Tickets\Application\Web\AbstractRequestHandler;
use PHPUGDD\PHPDD\Website\Tickets\Application\Web\Responses\Redirect;
use PHPUGDD\PHPDD\Website\Tickets\Application\Web\Tickets\Write\Validators\SelectTicketsValidator;
use PHPUGDD\PHPDD\Website\Tickets\Infrastructure\ErrorHandling\Severity;

final class SelectTicketsRequestHandler extends AbstractRequestHandler implements HandlesPostRequest
{
	private const FAIL_URL    = '/tickets/';

	private const SUCCESS_URL = '/tickets/details/';

	/**
	 * @param ProvidesWriteRequestData $request
	 */
	public function handle( ProvidesWriteRequestData $request )
	{
		$input            = $request->getInput();
		$session          = $this->getEnv()->getSession();
		$ticketSelectForm = $session->getTicketSelectionForm();
		$ticketSelectForm->resetFeedbacks();

		if ( $this->csrfCheckFailed( $ticketSelectForm, (string)$input->get( 'token' ) ) )
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

	private function csrfCheckFailed( Form $form, string $inputToken ) : bool
	{
		try
		{
			$token = Token::fromString( $inputToken );
		}
		catch ( InvalidTokenString $e )
		{
			$this->getEnv()->getErrorHandler()->captureException(
				$e,
				Severity::INFO,
				[
					'formId' => $form->getFormId()->toString(),
				]
			);

			return true;
		}

		return !$form->isTokenValid( $token );
	}

	private function getSelectedTickets( array $quantities ) : array
	{
		$selectedTickets = [];

		foreach ( $quantities as $type => $tickets )
		{
			foreach ( $tickets as $name => $quantity )
			{
				if ( 0 !== (int)$quantity )
				{
					$selectedTickets[ $type ][ $name ] = (int)$quantity;
				}
			}
		}

		return $selectedTickets;
	}
}