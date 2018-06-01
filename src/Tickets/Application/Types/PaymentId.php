<?php declare(strict_types=1);

namespace PHPUGDD\PHPDD\Website\Tickets\Application\Types;

use Fortuneglobe\Types\AbstractStringType;
use PHPUGDD\PHPDD\Website\Tickets\Application\Exceptions\InvalidArgumentException;

final class PaymentId extends AbstractStringType
{
	/**
	 * @param string $value
	 *
	 * @throws InvalidArgumentException
	 */
	protected function guardValueIsValid( string $value ) : void
	{
		if ( '' === trim( $value ) )
		{
			throw new InvalidArgumentException( 'Payment ID cannot be empty.' );
		}
	}
}