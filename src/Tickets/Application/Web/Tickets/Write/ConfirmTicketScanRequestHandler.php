<?php declare(strict_types=1);

namespace PHPUGDD\PHPDD\Website\Tickets\Application\Web\Tickets\Write;

use IceHawk\IceHawk\Interfaces\HandlesPostRequest;
use IceHawk\IceHawk\Interfaces\ProvidesWriteRequestData;
use PHPUGDD\PHPDD\Website\Tickets\Application\Tickets\Repositories\TicketOrderRepository;
use PHPUGDD\PHPDD\Website\Tickets\Application\Types\TicketItemId;
use PHPUGDD\PHPDD\Website\Tickets\Application\Web\AbstractRequestHandler;

final class ConfirmTicketScanRequestHandler extends AbstractRequestHandler implements HandlesPostRequest
{
	/**
	 * @param ProvidesWriteRequestData $request
	 *
	 * @throws \PHPUGDD\PHPDD\Website\Tickets\Application\Exceptions\RuntimeException
	 */
	public function handle( ProvidesWriteRequestData $request )
	{
		$ticketItemId = trim( $request->getInput()->get( 'ticketItemId' ) );
		$database     = $this->getEnv()->getDatabase();
		$repository   = new TicketOrderRepository( $database );

		$repository->confirmTicketScan( new TicketItemId( $ticketItemId ) );

		echo 'OK';
	}
}