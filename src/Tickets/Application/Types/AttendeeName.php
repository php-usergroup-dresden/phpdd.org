<?php declare(strict_types=1);
/**
 * @author hollodotme
 */

namespace PHPUGDD\PHPDD\Website\Tickets\Application\Types;

use Fortuneglobe\Types\AbstractStringType;
use PHPUGDD\PHPDD\Website\Tickets\Application\Exceptions\InvalidArgumentException;

/**
 * Class AttendeeName
 * @package PHPUGDD\PHPDD\Website\Tickets\Application\Types
 */
final class AttendeeName extends AbstractStringType
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
			throw new InvalidArgumentException( 'Attendee name cannot be empty.' );
		}
	}
}
