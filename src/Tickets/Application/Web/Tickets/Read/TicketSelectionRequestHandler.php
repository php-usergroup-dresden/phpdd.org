<?php declare(strict_types=1);
/**
 * @author hollodotme
 */

namespace PHPUGDD\PHPDD\Website\Tickets\Application\Web\Tickets\Read;

use IceHawk\IceHawk\Interfaces\HandlesGetRequest;
use IceHawk\IceHawk\Interfaces\ProvidesReadRequestData;
use PHPUGDD\PHPDD\Website\Tickets\Application\Configs\TicketsConfig;
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
	 * @throws \PHPUGDD\PHPDD\Website\Tickets\Application\Exceptions\RuntimeException
	 */
	public function handle( ProvidesReadRequestData $request )
	{
		$ticketsConfig = TicketsConfig::fromConfigFile();
		$data          = [
			'ticketConfigs' => $ticketsConfig->getTicketConfigs(),
		];

		(new HtmlPage( $this->getEnv() ))->respond( 'Tickets/Read/Pages/Selection.twig', $data );
	}
}
