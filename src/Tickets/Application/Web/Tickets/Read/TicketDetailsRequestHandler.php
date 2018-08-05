<?php declare(strict_types=1);

namespace PHPUGDD\PHPDD\Website\Tickets\Application\Web\Tickets\Read;

use IceHawk\IceHawk\Interfaces\HandlesGetRequest;
use IceHawk\IceHawk\Interfaces\ProvidesReadRequestData;
use PHPUGDD\PHPDD\Website\Tickets\Application\Configs\TicketsConfig;
use PHPUGDD\PHPDD\Website\Tickets\Application\Constants\CountryCodes;
use PHPUGDD\PHPDD\Website\Tickets\Application\Exceptions\RuntimeException;
use PHPUGDD\PHPDD\Website\Tickets\Application\Tickets\SelectedTicketInfos;
use PHPUGDD\PHPDD\Website\Tickets\Application\Web\AbstractRequestHandler;
use PHPUGDD\PHPDD\Website\Tickets\Application\Web\Responses\HtmlPage;
use PHPUGDD\PHPDD\Website\Tickets\Application\Web\Responses\Redirect;
use function array_combine;
use function count;

final class TicketDetailsRequestHandler extends AbstractRequestHandler implements HandlesGetRequest
{
	/**
	 * @param ProvidesReadRequestData $request
	 *
	 * @throws RuntimeException
	 * @throws \Twig_Error_Loader
	 * @throws \Twig_Error_Runtime
	 * @throws \Twig_Error_Syntax
	 */
	public function handle( ProvidesReadRequestData $request )
	{
		$session           = $this->getEnv()->getSession();
		$ticketSelectForm  = $session->getTicketSelectionForm();
		$ticketsConfig     = TicketsConfig::fromConfigFile();
		$selectedTickets   = (array)$ticketSelectForm->get( 'selectedTickets' );
		$ticketDetailsForm = $session->getTicketDetailsForm();
		$ticketDetailsForm->renewToken();

		if ( 0 === count( $selectedTickets ) )
		{
			(new Redirect())->respond( '/tickets/' );

			return;
		}

		$selectedTicketInfos = new SelectedTicketInfos( $ticketsConfig, $selectedTickets );

		$data = [
			'ticketOrderId'     => $ticketSelectForm->get( 'ticketOrderId' ),
			'selectedTickets'   => $selectedTicketInfos,
			'ticketDetailsForm' => $ticketDetailsForm,
			'countryCodes'      => array_combine( CountryCodes::ALL_SHORT, CountryCodes::ALL_LONG ),
		];

		(new HtmlPage( $this->getEnv() ))->respond( 'Tickets/Read/Pages/Details.twig', $data );
	}
}