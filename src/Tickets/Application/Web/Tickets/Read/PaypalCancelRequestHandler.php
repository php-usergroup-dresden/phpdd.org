<?php declare(strict_types=1);

namespace PHPUGDD\PHPDD\Website\Tickets\Application\Web\Tickets\Read;

use IceHawk\Forms\Feedback;
use IceHawk\IceHawk\Interfaces\HandlesGetRequest;
use IceHawk\IceHawk\Interfaces\ProvidesReadRequestData;
use PHPUGDD\PHPDD\Website\Tickets\Application\Web\AbstractRequestHandler;
use PHPUGDD\PHPDD\Website\Tickets\Application\Web\Responses\Redirect;

final class PaypalCancelRequestHandler extends AbstractRequestHandler implements HandlesGetRequest
{
	private const REDIRECT_URL = '/tickets/details/';

	public function handle( ProvidesReadRequestData $request )
	{
		$session           = $this->getEnv()->getSession();
		$ticketDetailsForm = $session->getTicketDetailsForm();

		$ticketDetailsForm->resetFeedbacks();
		$ticketDetailsForm->addFeedback(
			'paymentProvider',
			new Feedback( 'You canceled the paypal payment process.' )
		);

		(new Redirect())->respond( self::REDIRECT_URL );
	}
}