<?php declare(strict_types=1);

namespace PHPUGDD\PHPDD\Website\Tickets\Application\Web\Tickets\Read;

use IceHawk\IceHawk\Interfaces\HandlesGetRequest;
use IceHawk\IceHawk\Interfaces\ProvidesReadRequestData;
use PHPUGDD\PHPDD\Website\Tickets\Application\Exceptions\RuntimeException;
use PHPUGDD\PHPDD\Website\Tickets\Application\Web\AbstractRequestHandler;
use PHPUGDD\PHPDD\Website\Tickets\Application\Web\Responses\HtmlPage;

final class TicketDetailsRequestHandler extends AbstractRequestHandler implements HandlesGetRequest
{
	/**
	 * @param ProvidesReadRequestData $request
	 *
	 * @throws RuntimeException
	 */
	public function handle( ProvidesReadRequestData $request )
	{
		$session           = $this->getEnv()->getSession();
		$ticketSelectForm  = $session->getTicketSelectionForm();
		$selectedTickets   = $ticketSelectForm->get( 'selectedTickets' );
		$ticketDetailsForm = $session->getTicketDetailsForm();
		$ticketDetailsForm->renewToken();

		$data = [
			'selectedTickets'   => $selectedTickets,
			'ticketDetailsForm' => $ticketDetailsForm,
		];

		(new HtmlPage( $this->getEnv() ))->respond( 'Tickets/Read/Pages/Details.twig', $data );
	}
}