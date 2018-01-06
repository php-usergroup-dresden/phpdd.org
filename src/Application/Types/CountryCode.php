<?php declare(strict_types=1);
/**
 * @author hollodotme
 */

namespace PHPUGDD\PHPDD\Website\Application\Types;

use Fortuneglobe\Types\AbstractStringType;
use Fortuneglobe\Types\Exceptions\InvalidArgumentException;
use PHPUGDD\PHPDD\Website\Application\Constants\CountryCodes;

/**
 * Class CountryCode
 * @package PHPUGDD\PHPDD\Website\Application\Types
 */
final class CountryCode extends AbstractStringType
{
	/**
	 * @param string $value
	 *
	 * @throws InvalidArgumentException
	 */
	protected function guardValueIsValid( string $value ) : void
	{
		if ( !\in_array( $value, CountryCodes::ALL_SHORT, true ) )
		{
			throw new InvalidArgumentException( 'Invalid country code provided: ' . $value );
		}
	}
}
