<?php declare(strict_types=1);
/**
 * @author hollodotme
 */

namespace PHPUGDD\PHPDD\Website\Tickets\Application\Types;

use Fortuneglobe\Types\AbstractStringType;
use PHPUGDD\PHPDD\Website\Tickets\Application\Exceptions\InvalidArgumentException;

/**
 * Class StreetWithNumber
 * @package PHPUGDD\PHPDD\Website\Tickets\Application\Types
 */
final class StreetWithNumber extends AbstractStringType
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
			throw new InvalidArgumentException( 'Street with number cannot be empty.' );
		}
	}
}
