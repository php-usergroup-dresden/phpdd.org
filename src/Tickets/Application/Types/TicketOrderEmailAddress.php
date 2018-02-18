<?php declare(strict_types=1);
/**
 * @author hollodotme
 */

namespace PHPUGDD\PHPDD\Website\Tickets\Application\Types;

use Fortuneglobe\Types\AbstractStringType;
use Fortuneglobe\Types\Exceptions\InvalidArgumentException;

/**
 * Class TicketOrderEmailAddress
 * @package PHPUGDD\PHPDD\Website\Tickets\Application\Types
 */
final class TicketOrderEmailAddress extends AbstractStringType
{
	/**
	 * @param string $value
	 *
	 * @throws InvalidArgumentException
	 */
	protected function guardValueIsValid( string $value ) : void
	{
		if ( false === filter_var( $value, FILTER_VALIDATE_EMAIL ) )
		{
			throw new InvalidArgumentException( 'Invalid e-mail provided: ' . $value );
		}
	}
}
