<?php declare(strict_types=1);

namespace PHPUGDD\PHPDD\Website\Tickets\Application\Web\Tickets\Read;

use IceHawk\IceHawk\Interfaces\HandlesGetRequest;
use IceHawk\IceHawk\Interfaces\ProvidesReadRequestData;
use PHPUGDD\PHPDD\Website\Tickets\Application\Configs\DiscountsConfig;
use PHPUGDD\PHPDD\Website\Tickets\Application\Configs\TicketsConfig;
use PHPUGDD\PHPDD\Website\Tickets\Application\Exceptions\RuntimeException;
use PHPUGDD\PHPDD\Website\Tickets\Application\Tickets\TicketOrderBuilder;
use PHPUGDD\PHPDD\Website\Tickets\Application\Web\AbstractRequestHandler;
use PHPUGDD\PHPDD\Website\Tickets\Application\Web\Responses\HtmlPage;

final class TicketPaymentRequestHandler extends AbstractRequestHandler implements HandlesGetRequest
{
	/**
	 * @param ProvidesReadRequestData $request
	 *
	 * @throws RuntimeException
	 * @throws \Exception
	 */
	public function handle( ProvidesReadRequestData $request )
	{
		$session             = $this->getEnv()->getSession();
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

		$ticketPaymentForm->renewToken();

		$data = [
			'ticketPaymentForm' => $ticketPaymentForm,
			'ticketOrder'       => $ticketOrder,
		];

		(new HtmlPage( $this->getEnv() ))->respond( 'Tickets/Read/Pages/Payment.twig', $data );
	}
}