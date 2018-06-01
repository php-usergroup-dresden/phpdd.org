<?php declare(strict_types=1);

namespace PHPUGDD\PHPDD\Website\Tickets\Application\Web\Tickets\Read;

use IceHawk\IceHawk\Interfaces\HandlesGetRequest;
use IceHawk\IceHawk\Interfaces\ProvidesReadRequestData;
use PHPUGDD\PHPDD\Website\Tickets\Application\Web\AbstractRequestHandler;
use PHPUGDD\PHPDD\Website\Tickets\Application\Web\Responses\HtmlPage;
use PHPUGDD\PHPDD\Website\Tickets\Application\Web\Responses\Redirect;
use function dirname;

final class PurchaseDoneRequestHandler extends AbstractRequestHandler implements HandlesGetRequest
{
	public function handle( ProvidesReadRequestData $request )
	{
		$input         = $request->getInput();
		$ticketOrderId = (string)$input->get( 'ticketOrderId' );

		if ( '' === trim( $ticketOrderId ) )
		{
			(new Redirect())->respond( '/tickets/' );

			return;
		}

		$filePath = sprintf(
			'%s/%s.html',
			dirname( __DIR__, 6 ) . '/data/static/done',
			$ticketOrderId
		);

		(new HtmlPage( $this->getEnv() ))->respondWithFile( $filePath );
	}
}