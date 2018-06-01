<?php declare(strict_types=1);
/**
 * @author hollodotme
 */

namespace PHPUGDD\PHPDD\Website\Tickets\Application\Types;

use Fortuneglobe\Types\AbstractStringType;
use Fortuneglobe\Types\Exceptions\InvalidArgumentException;
use function preg_match;

/**
 * Class DiscountCode
 * @package PHPUGDD\PHPDD\Website\Tickets\Application\Types
 */
final class DiscountCode extends AbstractStringType
{
	/**
	 * @param string $value
	 *
	 * @throws InvalidArgumentException
	 */
	protected function guardValueIsValid( string $value ) : void
	{
		if ( !preg_match( '#^[A-Z]\d{3}18\d{3}[A-Z]$#', $value ) )
		{
			throw new InvalidArgumentException( 'Invalid discount code provided.' );
		}
	}
}
