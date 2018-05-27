<?php declare(strict_types=1);

namespace PHPUGDD\PHPDD\Website\Tickets\Application\Types;

use Fortuneglobe\Types\AbstractStringType;
use PHPUGDD\PHPDD\Website\Tickets\Application\Exceptions\InvalidArgumentException;

final class TicketId extends AbstractStringType
{
	/**
	 * @param string $value
	 *
	 * @throws InvalidArgumentException
	 */
	protected function guardValueIsValid( string $value ) : void
	{
		if ( !preg_match( '#^PHPDD18-(WS|EB|CT)-\d{2}$#', $value ) )
		{
			throw new InvalidArgumentException( 'Invalid ticket ID.' );
		}
	}
}