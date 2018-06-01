<?php declare(strict_types=1);

namespace PHPUGDD\PHPDD\Website\Tickets\Application\Web\Tickets\Read;

use IceHawk\IceHawk\Interfaces\HandlesGetRequest;
use IceHawk\IceHawk\Interfaces\ProvidesReadRequestData;
use PHPUGDD\PHPDD\Website\Tickets\Application\Configs\TicketsConfig;
use PHPUGDD\PHPDD\Website\Tickets\Application\Exceptions\RuntimeException;
use PHPUGDD\PHPDD\Website\Tickets\Application\Tickets\Repositories\TicketOrderRepository;
use PHPUGDD\PHPDD\Website\Tickets\Application\Tickets\TicketInfos;
use PHPUGDD\PHPDD\Website\Tickets\Application\Web\AbstractRequestHandler;
use PHPUGDD\PHPDD\Website\Tickets\Application\Web\Responses\HtmlPage;

/**
 * Class TicketSelectionRequestHandler
 * @package PHPUGDD\PHPDD\Website\Tickets\Application\Web\Tickets\Read
 */
final class TicketSelectionRequestHandler extends AbstractRequestHandler implements HandlesGetRequest
{
	/**
	 * @param ProvidesReadRequestData $request
	 *
	 * @throws RuntimeException
	 * @throws \Exception
	 */
	public function handle( ProvidesReadRequestData $request )
	{
		$session            = $this->getEnv()->getSession();
		$database           = $this->getEnv()->getDatabase();
		$ticketsConfig      = TicketsConfig::fromConfigFile();
		$reservationService = new TicketOrderRepository( $database );

		$ticketInfos = new TicketInfos( $ticketsConfig, $reservationService );

		$ticketSelectForm = $session->getTicketSelectionForm();
		$ticketSelectForm->renewToken();

		$data = [
			'ticketInfos'      => $ticketInfos->getTickets(),
			'ticketSelectForm' => $ticketSelectForm,
		];

		(new HtmlPage( $this->getEnv() ))->respond( 'Tickets/Read/Pages/Selection.twig', $data );
	}
}
