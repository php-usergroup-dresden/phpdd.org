<?php declare(strict_types=1);

namespace PHPUGDD\PHPDD\Website\Tickets\Application\Types;

use Fortuneglobe\Types\AbstractStringType;
use PHPUGDD\PHPDD\Website\Tickets\Application\Exceptions\InvalidArgumentException;

final class InvoiceId extends AbstractStringType
{
	/**
	 * @param string $value
	 *
	 * @throws InvalidArgumentException
	 */
	protected function guardValueIsValid( string $value ) : void
	{
		if ( !preg_match( '#^PHPDD18-\d{4}-\d{2}-\d{2}-\d{4}$#', $value ) )
		{
			throw new InvalidArgumentException( 'Invalid invoice id.' );
		}
	}
}