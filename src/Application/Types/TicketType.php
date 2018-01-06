<?php declare(strict_types=1);
/**
 * @author hollodotme
 */

namespace PHPUGDD\PHPDD\Website\Application\Types;

use Fortuneglobe\Types\AbstractStringType;
use Fortuneglobe\Types\Exceptions\InvalidArgumentException;
use PHPUGDD\PHPDD\Website\Application\Constants\TicketTypes;

/**
 * Class TicketType
 * @package PHPUGDD\PHPDD\Website\Application\Types
 */
final class TicketType extends AbstractStringType
{
	/**
	 * @param string $value
	 *
	 * @throws InvalidArgumentException
	 */
	protected function guardValueIsValid( string $value ) : void
	{
		if ( !\in_array( $value, TicketTypes::ALL, true ) )
		{
			throw new InvalidArgumentException( 'Invalid ticket type provided: ' . $value );
		}
	}
}
