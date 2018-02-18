<?php declare(strict_types=1);
/**
 * @author hollodotme
 */

namespace PHPUGDD\PHPDD\Website\Tickets\Application\Types;

use Fortuneglobe\Types\AbstractStringType;
use Fortuneglobe\Types\Exceptions\InvalidArgumentException;

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
		if ( 8 !== \strlen( $value ) )
		{
			throw new InvalidArgumentException( 'Invalid discount code provided.' );
		}

		if ( !preg_match( '#[A-Z]#', $value ) )
		{
			throw new InvalidArgumentException( 'Discount code has no uppercase letters.' );
		}

		if ( !preg_match( '#\d#', $value ) )
		{
			throw new InvalidArgumentException( 'Discount code has not digits.' );
		}
	}

	/**
	 * @return DiscountCode
	 * @throws \Exception
	 */
	public static function generate() : self
	{
		$chars  = '23456789ABCDEFGHJKLMNPQRSTUVWXYZ';
		$length = \strlen( $chars ) - 1;
		$code   = '';

		for ( $i = 0; $i < 8; $i++ )
		{
			$max   = $i === 1 ? 7 : $length;
			$index = random_int( 0, $max );
			$code  .= $chars[ $index ];
		}

		return new self( $code );
	}
}
