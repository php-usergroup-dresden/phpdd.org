<?php declare(strict_types=1);

namespace PHPUGDD\PHPDD\Website\Tickets\Application\Web\Responses;

use IceHawk\IceHawk\Constants\HttpCode;
use function flush;
use function header;
use function session_write_close;

final class Redirect
{
	public function respond( string $location, int $httpCode = HttpCode::MOVED_PERMANENTLY ) : void
	{
		if ( session_status() === PHP_SESSION_ACTIVE )
		{
			session_write_close();
		}

		header( 'Location: ' . $location, true, $httpCode );
		flush();
	}
}