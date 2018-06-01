<?php declare(strict_types=1);

namespace PHPUGDD\PHPDD\Website\Tickets\Application\Web\Tickets\Write;

use IceHawk\IceHawk\Interfaces\HandlesPostRequest;
use IceHawk\IceHawk\Interfaces\ProvidesWriteRequestData;
use PHPUGDD\PHPDD\Website\Tickets\Application\Web\AbstractRequestHandler;
use PHPUGDD\PHPDD\Website\Tickets\Application\Web\Responses\Redirect;

final class AbortTicketOrderRequestHandler extends AbstractRequestHandler implements HandlesPostRequest
{
	private const SUCESS_URL = '/tickets/';

	public function handle( ProvidesWriteRequestData $request )
	{
		$session = $this->getEnv()->getSession();
		$session->resetTicketOrder();

		(new Redirect())->respond( self::SUCESS_URL );
	}
}