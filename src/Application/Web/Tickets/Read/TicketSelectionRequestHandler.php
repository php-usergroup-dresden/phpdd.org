<?php declare(strict_types=1);
/**
 * @author hollodotme
 */

namespace PHPUGDD\PHPDD\Website\Application\Web\Tickets\Read;

use IceHawk\IceHawk\Interfaces\HandlesGetRequest;
use IceHawk\IceHawk\Interfaces\ProvidesReadRequestData;
use PHPUGDD\PHPDD\Website\Application\Configs\TicketsConfig;
use PHPUGDD\PHPDD\Website\Application\Web\AbstractRequestHandler;
use PHPUGDD\PHPDD\Website\Application\Web\Responses\HtmlPage;

/**
 * Class TicketSelectionRequestHandler
 * @package PHPUGDD\PHPDD\Website\Application\Web\Tickets\Read
 */
final class TicketSelectionRequestHandler extends AbstractRequestHandler implements HandlesGetRequest
{
	public function handle( ProvidesReadRequestData $request )
	{
		$ticketsConfig = new TicketsConfig();
		$data          = [
			'ticketConfigs' => $ticketsConfig->getTicketConfigs(),
		];

		(new HtmlPage( $this->getEnv() ))->respond( 'Tickets/Read/Pages/Selection.twig', $data );
	}
}
