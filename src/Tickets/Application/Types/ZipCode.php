<?php declare(strict_types=1);
/**
 * @author hollodotme
 */

namespace PHPUGDD\PHPDD\Website\Tickets\Application\Types;

use Fortuneglobe\Types\AbstractStringType;
use PHPUGDD\PHPDD\Website\Tickets\Application\Exceptions\InvalidArgumentException;

/**
 * Class ZipCode
 * @package PHPUGDD\PHPDD\Website\Tickets\Application\Types
 */
final class ZipCode extends AbstractStringType
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
			throw new InvalidArgumentException( 'Zip code cannot be empty.' );
		}
	}
}
